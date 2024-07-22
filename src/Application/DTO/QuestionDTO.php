<?php

declare(strict_types=1);

namespace App\Application\DTO;

class QuestionDTO
{
    public string $title;
    public string $link;

    public array $tags;


    public function __construct (string $title, string $link, array $tags)
    {
        $this->title = $title;
        $this->link = $link;
        $this->tags = $tags;
    }
}
