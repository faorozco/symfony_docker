<?php

namespace App\Repository;

use App\Entity\Entidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class EntidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entidad::class);
    }

    // /**
    //  * @return Eventos[] Returns an array of Eventos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Eventos
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getEntityById($em, $entidadId, $filter)
    {
        $result = $this->createQueryBuilder("e")
            ->where('e.id = :id')
            ->andWhere("e.estado_id = :estado_id")
            ->andWhere("e.nombre like :query")
            ->setParameter('id', $entidadId)
            ->setParameter('estado_id', 1)
            ->setParameter('query', "%" . $filter . "%")
            ->orderBy('e.nombre', "ASC")
            ->getQuery()
            ->execute();
        return $result[0];
    }
}
