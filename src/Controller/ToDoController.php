<?php

namespace App\Controller;

use App\Entity\ToDo;
use App\Entity\ToDoTitle;
use App\Form\TodoTaskType;
use App\Form\ToDoTitleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoController extends AbstractController
{
    #[Route('/todo', name: 'app_to_do')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->getUser();
            $userId = $user->getId();
        }

        $TodoTitleRepository = $entityManager->getRepository(ToDoTitle::class);
        $userTodoTitle = $TodoTitleRepository->findBy(['id_author' => $userId]);

            $TodoTitle = new ToDoTitle;
            $TodoTitleForm = $this->createForm(ToDoTitleType::class, $TodoTitle);
            $TodoTitleForm->handleRequest($request);
        
            if ($TodoTitleForm->isSubmitted() && $TodoTitleForm->isValid()) {
                $TodoTitle->setIdAuthor($userId);
                $TodoTitle->setCreatedAt(new \DateTimeImmutable());
        
                $entityManager->persist($TodoTitle);
                $entityManager->flush();
            }

        

        $TodoTaskRepositry = $entityManager->getRepository(ToDo::class);
        $TodoList = $TodoTaskRepositry->findBy(['id_title' => $TodoIdTitle->getIdTitle()]);

        $TodoTask = new ToDo;
        $TodoTaskForm =$this->createForm(TodoTaskType::class, $TodoTask);
        $TodoTaskForm->handleRequest($request);

        if ($TodoTaskForm->isSubmitted() && $TodoTaskForm->isValid()) {
            
            $entityManager->persist($TodoTask);
            $entityManager->flush();
        }

        dd($TodoIdTitle);

        return $this->render('to_do/index.html.twig', [
            'controller_name' => 'ToDoController',
            'TitleForm' => $TodoTitleForm->createView(),
            'TaskForm' => $TodoTaskForm->createView(),
            'TodoTitle' => $userTodoTitle,
            'todo' => $TodoList,
        ]);
    }
}
