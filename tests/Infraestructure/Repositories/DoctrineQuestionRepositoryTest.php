<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repositories;

use App\Domain\Entities\Question;
use App\Infrastructure\Repositories\DoctrineQuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineQuestionRepositoryTest extends KernelTestCase
{
    private DoctrineQuestionRepository $repository;
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $managerRegistry;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->managerRegistry = self::getContainer()->get(ManagerRegistry::class);
        $this->entityManager = $this->managerRegistry->getManager();
        $this->assertNotNull($this->entityManager, 'EntityManager should not be null.');

        $this->repository = new DoctrineQuestionRepository($this->managerRegistry,$this->entityManager);
    }

    public function testSaveAndFind(): void
    {
        $question = new Question('Sample Title', 'examplelink.com', ['tag1', 'tag2']);

        $this->repository->save($question);

        $savedQuestion = $this->repository->find($question->getId());

        $this->assertNotNull($savedQuestion);
        $this->assertSame('Sample Title', $savedQuestion->getTitle());
        $this->assertSame('examplelink.com', $savedQuestion->getLink());
        $this->assertSame(['tag1','tag2'], $savedQuestion->getTags());
    }

    public function testFindByCriteria(): void
    {
        $question = new Question('Sample Title test find by criteria',
            'examplelink.com', ['tag1', 'tag2']);
        $this->repository->save($question);

        $criteria = ['title' => 'Sample Title test find by criteria'];
        $foundQuestions = $this->repository->findByCriteria($criteria);

        $this->assertNotEmpty($foundQuestions);
        $this->assertSame('Sample Title test find by criteria', $foundQuestions[0]->getTitle());
    }
}
