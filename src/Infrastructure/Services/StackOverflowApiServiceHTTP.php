<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\Services\StackOverflowApiServiceInterface;
use App\Application\DTO\QuestionDTO;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * @implements StackOverflowApiServiceInterface
 */
class StackOverflowApiServiceHTTP implements StackOverflowApiServiceInterface
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private string $apiUrl = 'https://api.stackexchange.com/2.3/questions';

    public function __construct(HttpClientInterface $client,LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchQuestions(array $filters): array
    {
            try {
                $response = $this->client->request('GET', $this->apiUrl, [
                    'query' => array_merge([
                        'order' => 'desc',
                        'sort' => 'creation',
                        'site' => 'stackoverflow'
                    ], $filters)
                ]);
                $data = $response->toArray();
            } catch (\Exception $e) {
                $this->logger->error('Error fetching questions: ' . $e->getMessage(), [
                    'exception' => $e,
                    'filters' => $filters,
                ]);
                throw new \Exception('Error fetching data from Stack Overflow API: ' . $e->getMessage(), 0, $e);
            }
            $questions = [];
            foreach ($data['items'] as $item) {
                $questions[] = new QuestionDTO(
                    $item['title'],
                    $item['link'],
                    $item['tags']
                );
            }

            return $questions;
    }
}
