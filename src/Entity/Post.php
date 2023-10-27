<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PosteRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PosteRepository::class)]
class Post
{
    #[Groups(['post:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['post:read'])]
    #[Assert\NotNull(message: "Titre obligatoire")]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['post:read'])]
    #[Assert\NotNull(message: "Content obligatoire")]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}