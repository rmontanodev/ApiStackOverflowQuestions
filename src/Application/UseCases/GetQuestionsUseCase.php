<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entities\Question;
use App\Domain\Repositories\QuestionRepositoryInterface;
use App\Infrastructure\Services\StackOverflowApiServiceHTTP;
use Psr\Log\LoggerInterface;

class GetQuestionsUseCase
{
    private QuestionRepositoryInterface $questionRepository;
    private StackOverflowApiServiceHTTP $stackOverflowApiService;
    private LoggerInterface $logger;

    public function __construct(
        QuestionRepositoryInterface $questionRepository,
        StackOverflowApiServiceHTTP $stackOverflowApiService,
        LoggerInterface $logger
    ) {
        $this->questionRepository = $questionRepository;
        $this->stackOverflowApiService = $stackOverflowApiService;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function execute(array $filters): array
    {
        if (!isset($filters['tagged'])) {
            $this->logger->warning('Missing "tagged" filter', $filters);
            throw new \InvalidArgumentException('The "tagged" filter is required.');
        }

        $cachedQuestions = $this->questionRepository->findByCriteria($filters);
        if (!empty($cachedQuestions)) {
            return $cachedQuestions;
        }

        $questionsDTO = $this->stackOverflowApiService->fetchQuestions($filters);
        $questions = [];
        foreach ($questionsDTO as $dto) {
            $question = new Question($dto->title,$dto->link,$dto->tags);
            $this->questionRepository->save($question);
            $questions[] = $question;
        }
        $this->logger->info('Fetched questions', ['count' => count($questions)]);

        return $questions;
    }
}
