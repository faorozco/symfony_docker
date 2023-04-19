<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Lista;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class ListaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lista::class);
    }

    public function findListas($queryString, $page, $itemsPerPage, $estado, $orderBy)
    {
        $query = $this->createQueryBuilder("l");
        $query->where("l.nombre like :query");
        $query->andWhere("l.estado_id = :estado_id");
        $query->setParameter('estado_id', $estado);
        $query->setParameter('query', "%" . $queryString . "%");
        $query->setFirstResult(($page - 1) * $itemsPerPage);
        if (null !== $orderBy) {
            $query->orderBy('l.nombre', $orderBy);
        }
        $query->setMaxResults($itemsPerPage);
        $query->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }
}
