<?php

declare(strict_types=1);

namespace App\Tests\Application\UseCases;

use App\Application\DTO\QuestionDTO;
use App\Application\UseCases\GetQuestionsUseCase;
use App\Domain\Entities\Question;
use App\Domain\Repositories\QuestionRepositoryInterface;
use App\Infrastructure\Services\StackOverflowApiServiceHTTP;
use App\Tests\Factory\QuestionFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GetQuestionsUseCaseTest extends TestCase
{
    private GetQuestionsUseCase $useCase;
    private QuestionRepositoryInterface $questionRepositoryMock;
    private StackOverflowApiServiceHTTP $apiServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->questionRepositoryMock = $this->createMock(QuestionRepositoryInterface::class);
        $this->apiServiceMock = $this->createMock(StackOverflowApiServiceHTTP::class);
        $logger = $this->createMock(LoggerInterface::class);
        $this->useCase = new GetQuestionsUseCase($this->questionRepositoryMock, $this->apiServiceMock, $logger);
    }

    public function testExecuteWithCachedQuestions(): void
    {
        $filters = ['tagged' => 'php'];
        $cachedQuestions = [QuestionFactory::create()];

        $this->questionRepositoryMock
            ->method('findByCriteria')
            ->with($filters)
            ->willReturn($cachedQuestions);
        $questions = [];
        try{
            $questions = $this->useCase->execute($filters);
        }catch(\Exception $e){
            $this->assertTrue(false);
        }

        $this->assertSame($cachedQuestions, $questions);
    }

    public function testExecuteWithApiCall(): void
    {
        $filters = ['tagged' => 'php'];
        $questionsDTO = [new QuestionDTO('Test Question','examplelink.com',  ['php'])];

        $this->questionRepositoryMock
            ->method('findByCriteria')
            ->with($filters)
            ->willReturn([]);


        $this->apiServiceMock
            ->method('fetchQuestions')
            ->with($filters)
            ->willReturn($questionsDTO);

        $this->questionRepositoryMock
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->callback(function (Question $question) {
                return $question->getLink() === 'examplelink.com';
            }));
        try{
            $questions = $this->useCase->execute($filters);
        }catch (\Exception $e){
            $this->assertTrue(true);
        }

        $this->assertCount(1, $questions);
        $this->assertInstanceOf(Question::class, $questions[0]);
    }
    public function testExecuteWithoutTaggedFilter()
    {
        try{
            $this->useCase->execute([]);
        }catch (\Exception $e){
            $this->assertTrue(true);
        }
    }
}
