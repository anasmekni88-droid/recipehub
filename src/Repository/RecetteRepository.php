<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recette>
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    public function findPublished(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.publiee = :val')
            ->setParameter('val', true)
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findDrafts(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.publiee = :val')
            ->setParameter('val', false)
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLastPublished(int $limit = 6): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.publiee = :val')
            ->setParameter('val', true)
            ->orderBy('r.dateCreation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
