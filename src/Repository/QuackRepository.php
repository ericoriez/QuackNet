<?php

namespace App\Repository;

use App\Entity\Quack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quack>
 */
class QuackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quack::class);
    }
    public function findAllMainQuacks(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.parent IS NULL') // Filtre pour les quacks sans parent
            ->orderBy('q.created_at', 'DESC') // Tri par date de création
            ->getQuery()
            ->getResult(); // Retourne une liste
    }
    public function findWithReplies(int $id): ?Quack
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.replies', 'r') // Joint les réponses
            ->addSelect('r') // Ajoute les réponses à la sélection
            ->where('q.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(); // Retourne un seul résultat ou null
    }
    public function findByTag(string $tagName): array
    {
        return $this->createQueryBuilder('q')
            ->join('q.tags', 't') // Joint la table des tags
            ->where('t.name = :tagName')
            ->setParameter('tagName', $tagName)
            ->orderBy('q.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findVisibleQuacks(): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.isModerated = false')
            ->orderBy('q.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Quack[] Returns an array of Quack objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Quack
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
