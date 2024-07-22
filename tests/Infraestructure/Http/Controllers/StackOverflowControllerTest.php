<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Http\Controllers;

use App\Application\UseCases\GetQuestionsUseCase;
use App\Infrastructure\Http\Controllers\StackOverFlowController;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StackOverflowControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MockObject $getQuestionsUseCaseMock;
    private MockObject $loggerMock;
    private StackOverflowController $controller;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->getQuestionsUseCaseMock = $this->createMock(GetQuestionsUseCase::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $this->controller = new StackOverflowController(
            $this->getQuestionsUseCaseMock,
            $validatorMock,
            $this->loggerMock
        );
    }


    public function testGetQuestionsWithValidData(): void
    {
        try {
            $this->client->request('GET', '/api/questions', [
                'tagged' => ['php'],
                'todate' => '2024-01-01',
                'fromdate' => '2023-01-01',
            ]);
            $response = $this->client->getResponse();
        }catch (\Exception $e){
            $this->assertTrue(false);
        }
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testGetQuestionsWithInvalidData(): void
    {
        $this->client->request('GET', '/api/questions', [
            'query' => [
                'tagged' => '',
            ]
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('[tagged]', $data['errors']);
        $this->assertArrayHasKey('[query]', $data['errors']);
    }
    public function testGetQuestionsHandlesException(): void
    {
        $this->getQuestionsUseCaseMock
            ->method('execute')
            ->willThrowException(new \RuntimeException('Test exception'));

        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error in GetQuestionsUseCase'));

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'GET']);
        $response = $this->controller->getQuestions($request);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
