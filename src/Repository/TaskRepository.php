<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use DateTime;
use Exception;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getNextUuid(): UuidInterface
    {
        return Uuid::uuid4();
    }

    public function persist(Task $task): void
    {
        $em = $this->getEntityManager();
        $em->persist($task);
        $em->flush();
    }

    public function existsScheduledTaskAt(DateTime $date): bool
    {
        $em = $this->getEntityManager();

        $exists = $em->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.dueDate = :date')
            ->andWhere('t.hour = :hour')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('hour', $date->format('H:i'))
            ->getQuery()
            ->getOneOrNullResult();

        return $exists !== null;
    }

    public function getAll(): array
    {
        $em = $this->getEntityManager();

        $taskRepository = $em->getRepository(Task::class);

        return $taskRepository->findAll();
    }

    public function getById(string $id): array
    {
        $em = $this->getEntityManager();

        $task = $em->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.uuid = :uuid')
            ->setParameter('uuid', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $task ? $task->toArray() : [];
    }

    public function deleteById(string $id): void
    {
        $em = $this->getEntityManager();

        $task = $em->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.uuid = :uuid')
            ->setParameter('uuid', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$task) {
            throw new Exception("Não existe task com id $id", 404);
        }

        $em->remove($task);
        $em->flush();
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
