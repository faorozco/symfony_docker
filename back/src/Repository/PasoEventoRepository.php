<?php

namespace App\Repository;

use App\Entity\PasoEvento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PasoEvento|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasoEvento|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasoEvento[]    findAll()
 * @method PasoEvento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasoEventoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasoEvento::class);
    }

    // /**
    //  * @return PasoEvento[] Returns an array of PasoEvento objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PasoEvento
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
