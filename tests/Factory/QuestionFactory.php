<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Entities\Question;

class QuestionFactory
{
    public static function create(
        string $title = 'Sample Title',
        string $link = 'examplelink.com',
        array $tags = ['php'],
    ): Question {
        return new Question($title, $link, $tags);
    }
}
