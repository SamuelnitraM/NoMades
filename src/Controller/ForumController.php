<?php

namespace App\Controller;

use App\Entity\AnswerPost;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Form\AnswerType;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function Forum(EntityManagerInterface $entityManager, Request $request): Response
    {
 
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->findAll();
        
        $postRepository = $entityManager->getRepository(Post::class);
    
        $categoryPostCounts = [];
        foreach ($category as $categories) {
            $categoryPostCounts[$categories->getId()] = $postRepository->count(['category' => $categories]);
        }
    
        return $this->render('forum/forum.html.twig', [
            'controller_name' => 'ForumController',
            'category' => $category,
            'numPost' => $categoryPostCounts,
        ]);
    }

    #[Route('/forum/{id}', name: 'app_post_list')]
    public function postList($id, EntityManagerInterface $entityManager, Request $request, Category $category): Response
    {

        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->find($id);


        if (!$category) {
            throw $this->createNotFoundException('Attention filou ! La catégorie demandée n\'existe pas !');
        }

        $postRepository = $entityManager->getRepository(Post::class);
        $allPost = $postRepository->findBy(['category' => $category]);



        $username = [];
        foreach ($allPost as $post) {
            $userId = $post->getIdAuthor();
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($userId);
            $username[$post->getId()] = $user->getUsername();

            $postId = $post->getId();

            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($post->getIdAuthor());
            $username[$postId] = $user->getUsername();

            $answerRepository = $entityManager->getRepository(AnswerPost::class);
            $numResponses[$postId] = count($answerRepository->findBy(['post' => $post]));
        }
        
        $user = $this->getUser();

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $post->setCategory($category);
            $post->setIdAuthor($user->getId());
            $post->setCreatedAt(new \DateTimeImmutable());

            $description = $form->get('description')->getData();
            $post->setDescription($description);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_list', ['id' => $id]);
        }
        return $this->render('forum/post_list.html.twig', [
            'controller_name' => 'ForumController',
            'category' => $category,
            'form' => $form->createView(),
            'post' => $allPost,
            'user' => $username,
            'AnswerCount' => $numResponses,

        ]);
    }

    #[Route('/post/{id}', name: 'app_post')]
    public function post($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $postRepository = $entityManager->getRepository(Post::class);
        $post = $postRepository->find($id);

        $userRepository = $entityManager->getRepository(User::class);
        $username = $userRepository->find($post->getIdAuthor());

        $AnswerRepository = $entityManager->getRepository(AnswerPost::class);
        $answers = $AnswerRepository->findby(['post' => $post]);

        $answerAuthor = [];
        foreach ($answers as $answer) {
            $userId = $answer->getIdAuthor();
            $userRepo = $entityManager->getRepository(User::class);
            $user = $userRepo->find($userId);
            $answerAuthor[$answer->getId()] = $user->getUsername();
        }

        $user = $this->getUser();

        $newAnswer = new AnswerPost();
        $form = $this->createForm(AnswerType::class, $newAnswer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newAnswer->setPost($post);
            $newAnswer->setIdAuthor($user->getId());
            $newAnswer->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($newAnswer);
            $entityManager->flush();

            return $this->redirectToRoute('app_post', ['id' => $id]);
        }

        return $this->render('forum/post.html.twig', [
            'post' => $post,
            'user' => $username,
            'answerAuthor' => $answerAuthor,
            'answers' => $answers,
            'form' => $form->createView()
        ]);
    }
}
