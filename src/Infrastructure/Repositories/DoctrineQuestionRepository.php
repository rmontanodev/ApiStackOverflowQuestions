<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Question;
use App\Domain\Repositories\QuestionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method string getClassName() Returns the class name of the entity.
 * @method mixed matching(Criteria $criteria) Returns an array of entities matching the given criteria.
 */
class DoctrineQuestionRepository extends ServiceEntityRepository implements QuestionRepositoryInterface
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $em)
    {
        parent::__construct($registry, Question::class);
        $this->em = $em;
    }

    public function save(Question $question): void
    {
        $this->em->persist($question);
        $this->em->flush();
    }

    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('q');

        if (isset($criteria['tagged'])) {
            $qb->andWhere('q.tags IN (:tags)')
                ->setParameter('tags', $criteria['tagged']);
        }
        if (isset($criteria['title'])) {
            $qb->andWhere('q.title = :title')
                ->setParameter('title', $criteria['title']);
        }

        if (isset($criteria['todate'])) {
            $qb->andWhere('q.createdAt <= :todate')
                ->setParameter('todate', $criteria['todate']);
        }

        if (isset($criteria['fromdate'])) {
            $qb->andWhere('q.createdAt >= :fromdate')
                ->setParameter('fromdate', $criteria['fromdate']);
        }

        return $qb->getQuery()->getResult();
    }
}
