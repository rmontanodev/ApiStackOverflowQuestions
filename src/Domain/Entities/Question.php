<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: "App\Domain\Repositories\QuestionRepositoryInterface")]
#[ORM\Table(name: "questions")]
class Question
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;


    #[ORM\Column(type: "string", length: 255)]
    private string $title;


    #[ORM\Column(type: "text")]
    private string $link;


    #[ORM\Column(type: "json")]
    private array $tags;


    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;


    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $updatedAt;

    public function __construct(string $title, string $link, array $tags)
    {
        $this->title = $title;
        $this->link = $link;
        $this->tags = $tags;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
