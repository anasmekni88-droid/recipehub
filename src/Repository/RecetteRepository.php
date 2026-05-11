<?php

namespace App\Repository;

use App\Entity\CategorieRecette;
use App\Entity\Recette;
use App\Entity\TagRecette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findByFilters(?string $titre, ?CategorieRecette $cat, ?string $diff, ?TagRecette $tag): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.publiee = true');

        if ($titre) {
            $qb->andWhere('r.titre LIKE :titre')
               ->setParameter('titre', '%' . $titre . '%');
        }
        if ($cat) {
            $qb->andWhere('r.categorie = :cat')
               ->setParameter('cat', $cat);
        }
        if ($diff) {
            $qb->andWhere('r.difficulte = :diff')
               ->setParameter('diff', $diff);
        }
        if ($tag) {
            $qb->innerJoin('r.tags', 't')
               ->andWhere('t = :tag')
               ->setParameter('tag', $tag);
        }

        return $qb->orderBy('r.dateCreation', 'DESC')
                  ->getQuery()->getResult();
    }

    public function findByFiltersQueryBuilder(?string $titre, ?CategorieRecette $cat, ?string $diff, ?TagRecette $tag): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.publiee = true');

        if ($titre) {
            $qb->andWhere('r.titre LIKE :titre')
               ->setParameter('titre', '%' . $titre . '%');
        }
        if ($cat) {
            $qb->andWhere('r.categorie = :cat')
               ->setParameter('cat', $cat);
        }
        if ($diff) {
            $qb->andWhere('r.difficulte = :diff')
               ->setParameter('diff', $diff);
        }
        if ($tag) {
            $qb->innerJoin('r.tags', 't')
               ->andWhere('t = :tag')
               ->setParameter('tag', $tag);
        }

        return $qb->orderBy('r.dateCreation', 'DESC');
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
