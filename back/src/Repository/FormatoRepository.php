<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Formato;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class FormatoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formato::class);
    }

    public function findByRegistroId($queryString, $page, $orderBy, $itemsPerPage, $registroId)
    {
        $query = $this->createQueryBuilder("f")
        ->leftJoin('f.plantillaVersion', 'plantilla')
        ->addSelect('plantilla')
        ->where("f.registro_id = :registroId")
        ->andWhere("f.estado_id = :estado_id");
        if(trim($queryString) != "") {
            $query->andWhere("f.titulo like :queryString OR plantilla.descripcion like :queryString");
            $query->setParameter('queryString', "%" . $queryString . "%");
        }
        $query->setParameter('estado_id', 1)
        ->setParameter('registroId', $registroId)
        ->select("f.id as id, f.cuando as cuando, f.titulo as titulo, plantilla.descripcion as descripcion");
        if (null !== $orderBy) {
            $query->orderBy('f.' . key($orderBy), $orderBy[key($orderBy)]);
        }
        $query->setFirstResult(($page - 1) * $itemsPerPage)
        ->setMaxResults($itemsPerPage)
        ->getQuery();

        //Si no tiene ningun acceso mostrar solo el archivo marcado como tipo documental

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }
}
