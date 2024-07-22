<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entities;

use App\Tests\Factory\QuestionFactory;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $question = QuestionFactory::create();
        $question->setTitle('Test Question');
        $question->setLink('examplelink.com');
        $question->setTags(['php', 'symfony']);

        $this->assertEquals('Test Question', $question->getTitle());
        $this->assertEquals('examplelink.com', $question->getLink());
        $this->assertEquals(['php','symfony'], $question->getTags());
    }
}
