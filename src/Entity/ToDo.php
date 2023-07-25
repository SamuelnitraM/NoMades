<?php

namespace App\Entity;

use App\Repository\ToDoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToDoRepository::class)]
class ToDo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_title = null;

    #[ORM\Column(nullable: true)]
    private ?int $progress = null;

    #[ORM\Column(length: 1500)]
    private ?string $body = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTitle(): ?int
    {
        return $this->id_title;
    }

    public function setIdTitle(int $id_title): self
    {
        $this->id_title = $id_title;

        return $this;
    }

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(?int $progress): self
    {
        $this->progress = $progress;

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
}
