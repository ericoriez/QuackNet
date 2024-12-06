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
            ->orderBy('q.createdAt', 'DESC') // Tri par date de création
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
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findVisibleQuacks(): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.isModerated = false')
            ->andWhere('q.isComment = false') // Exclut les commentaires
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.author', 'a')
            ->leftJoin('q.tags', 't')
            ->where('a.duckname LIKE :keyword')
            ->orWhere('t.name LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }
    /**
     * Recherche des quacks visibles avec un mot-clé.
     *
     * @param string $keyword Le mot-clé à rechercher.
     * @return Quack[]
     */
    public function searchVisibleQuacks(string $keyword): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.author', 'a')
            ->leftJoin('q.tags', 't')
            ->where('q.isModerated = false') // Filtrer les quacks non modérés
            ->andWhere('a.duckname LIKE :keyword OR t.name LIKE :keyword OR q.content LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('q.createdAt', 'DESC')
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
