<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Question;

interface QuestionRepositoryInterface
{
    public function save(Question $question): void;
    public function findByCriteria(array $criteria): array;
}
