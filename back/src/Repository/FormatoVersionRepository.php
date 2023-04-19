<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\FormatoVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class FormatoVersionRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormatoVersion::class);
    }

    public function findByRegistroId($queryString, $page, $orderBy, $itemsPerPage, $registroId)
    {
        $query = $this->createQueryBuilder("fv");
        $query->where("fv.registro_id = :registroId");
        $query->andWhere("fv.estado_id = :estado_id");
        $query->setParameter('estado_id', 1);
        $query->setParameter('registroId', $registroId);
        if (null !== $orderBy) {
            $query->orderBy('fv.' . key($orderBy), $orderBy[key($orderBy)]);
        }
        $query->setFirstResult(($page - 1) * $itemsPerPage);
        $query->setMaxResults($itemsPerPage);
        $query->getQuery();

        //Si no tiene ningun acceso mostrar solo el archivo marcado como tipo documental

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }
}
