<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?int $id_author = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'json', nullable: true)] 
    private ?array $description = [];

    #[ORM\Column(length: 1500)]
    private ?string $body = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: AnswerPost::class, orphanRemoval: true)]
    private Collection $answerPosts;

    public function __construct()
    {
        $this->answerPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAuthor(): ?int
    {
        return $this->id_author;
    }

    public function setIdAuthor(int $id_author): self
    {
        $this->id_author = $id_author;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, AnswerPost>
     */
    public function getAnswerPosts(): Collection
    {
        return $this->answerPosts;
    }

    public function addAnswerPost(AnswerPost $answerPost): self
    {
        if (!$this->answerPosts->contains($answerPost)) {
            $this->answerPosts->add($answerPost);
            $answerPost->setPost($this);
        }

        return $this;
    }

    public function removeAnswerPost(AnswerPost $answerPost): self
    {
        if ($this->answerPosts->removeElement($answerPost)) {
            // set the owning side to null (unless already changed)
            if ($answerPost->getPost() === $this) {
                $answerPost->setPost(null);
            }
        }

        return $this;
    }

}
