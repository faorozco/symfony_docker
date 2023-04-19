<?php

namespace App\Repository;

use App\Entity\Ciudad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class CiudadRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ciudad::class);
    }

    public function listar($filter, $order, $mostrarInactivos)
    {
        if($order === 'asc') {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        if($mostrarInactivos == "true") {
            $estado = 0;
        } else {
            $estado = 1;
        }

        $query = $this->createQueryBuilder("c")
        ->select("c.id, 
                  c.nombre, 
                  c.codigo
                  ")
        ->where("c.nombre LIKE :query or c.codigo LIKE :query ") 
        ->andWhere("c.estado_id = :estado ")                            
        ->setParameter('query', "%" . $filter . "%")
        ->setParameter('estado', $estado)
        ->orderBy('c.codigo', $order)
        ->getQuery();


        return $query->execute();
    }

    public function listarPage($filter, $order, $mostrarInactivos, $page, $size)
    {
        if($order === 'asc') {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        if($mostrarInactivos == "true") {
            $estado = 0;
        } else {
            $estado = 1;
        }

        $query = $this->createQueryBuilder("c")
        ->select("c.id, 
                  c.nombre, 
                  c.codigo
                  ")
        ->where("c.nombre LIKE :query or c.codigo LIKE :query ") 
        ->andWhere("c.estado_id = :estado ")                            
        ->setParameter('query', "%" . $filter . "%")
        ->setParameter('estado', $estado)
        ->orderBy('c.codigo', $order)
        ->setFirstResult(($page - 1) * $size)
        ->setMaxResults($size)
        ->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);


        return $result;
    }
}
