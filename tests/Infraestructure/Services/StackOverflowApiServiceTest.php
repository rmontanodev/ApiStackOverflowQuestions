<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Services;

use App\Infrastructure\Services\StackOverflowApiServiceHTTP;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\MockHttpClient;

class StackOverflowApiServiceTest extends KernelTestCase
{
    private StackOverflowApiServiceHTTP $apiService;
    private HttpClientInterface $realHttpClient;
    private MockObject $mockHttpClient;
    private MockObject $mockLogger;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->realHttpClient = self::getContainer()->get(HttpClientInterface::class);
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->apiService = new StackOverflowApiServiceHTTP($this->realHttpClient, $this->mockLogger);
    }

    public function testFetchQuestionsWithRealClient(): void
    {
        $questions = $this->apiService->fetchQuestions(['tagged' => 'php']);

        $this->assertIsArray($questions);
        $this->assertNotEmpty($questions);

        foreach ($questions as $question) {
            $this->assertNotEmpty('title', $question->title);
            $this->assertNotEmpty('link', $question->link);
            $this->assertIsArray($question->tags);
        }
    }
    public function testFetchQuestionsHandlesTransportException(): void
    {
        $this->apiService = new StackOverflowApiServiceHTTP($this->mockHttpClient, $this->mockLogger);
        $this->mockHttpClient
            ->method('request')
            ->willThrowException($this->createMock(TransportExceptionInterface::class));

        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('Error fetching questions:'),
                $this->arrayHasKey('exception')
            );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error fetching data from Stack Overflow API:');

        dd($this->apiService->fetchQuestions(['tagged' => 'php']));
    }

}
