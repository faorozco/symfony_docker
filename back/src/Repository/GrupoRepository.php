<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Grupo;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\ORM\Query\Expr;

class GrupoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grupo::class);
    }

    public function findAllGroups($queryString, $page, $itemsPerPage, $estado, $orderBy)
    {
        $query = $this->createQueryBuilder("g")
        ->select("g.modo,
                  g.nombre,
                  g.id,
                  g.estado_id AS estadoId,
                  GROUP_CONCAT('{','$','id','$',':','$',u.id,'$',' , ','$','nombre1','$',':','$',u.nombre1,' ',u.nombre2,' ',u.apellido1,' ',u.apellido2,' ','$','}') AS usuarios")
        ->join('g.usuarios', 'u')
        ->where("g.nombre like :query")
        ->andWhere("g.estado_id = :estado_id")
        ->groupBy('g.id') // agregar la cláusula GROUP BY
        ->setParameter('estado_id', $estado)
        ->setParameter('query', "%" . $queryString . "%")
        ->setFirstResult(($page - 1) * $itemsPerPage);
        if (null !== $orderBy) {
            $query->orderBy('g.id', $orderBy);
        }
        $query->setMaxResults($itemsPerPage)
        ->getQuery();
        $doctrinePaginator = new DoctrinePaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findOneId($id)
    {
        $query = $this->createQueryBuilder("g")
        ->select("g.modo,
                  g.nombre,
                  g.id,
                  g.estado_id AS estadoId,
                  u.id,
                  u.nombre1,
                  u.nombre2,
                  u.apellido1,
                  u.apellido2")
        ->join('g.usuarios', 'u')
        ->where("g.id = :id")
        ->setParameter('id', $id)
        ->getQuery();

    return $query->getResult();
    }

}
