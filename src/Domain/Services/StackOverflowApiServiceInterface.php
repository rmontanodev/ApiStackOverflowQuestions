<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Application\DTO\QuestionDTO;

interface StackOverflowApiServiceInterface
{
    /**
     * Fetch questions from Stack Overflow API.
     *
     * @param array $filters The filters to apply to the API request.
     * @return QuestionDTO[] The list of questions.
     * @throws \Exception If an error occurs during the API request.
     */
    public function fetchQuestions(array $filters): array;
}
