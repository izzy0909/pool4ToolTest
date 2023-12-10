<?php

namespace App\Repository;

use App\Entity\CronJobs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CronJobs|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronJobs|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronJobs[]    findAll()
 * @method CronJobs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CronJobsRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CronJobs::class);

        $this->entityManager = $this->getEntityManager();
    }

    /**
     * @param $cronJob
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($cronJob)
    {
        $this->entityManager->persist($cronJob);
        $this->entityManager->flush();
    }

    /**
     * @param $cronJob
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove($cronJob)
    {
        $this->entityManager->remove($cronJob);
        $this->entityManager->flush();
    }

    /**
     * @return CronJobs|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneCronJobToRun(): ?CronJobs
    {
        return $this->createQueryBuilder('cj')
            ->andWhere('cj.status = :status')
            ->setParameter('status', CronJobs::STATUS_PENDING)
            ->orderBy('cj.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
