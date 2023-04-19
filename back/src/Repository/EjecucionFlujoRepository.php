<?php

namespace App\Repository;

use App\Entity\EjecucionFlujo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

class EjecucionFlujoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EjecucionFlujo::class);
    }

    public function buscarPasoRadicado($radicado, $filter, $order)
    {
        if($order === 'asc') {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        $query = $this->createQueryBuilder("ep")
        ->select("ef.id, 
                  ef.flujo_trabajo_version_id, 
                  ftv.nombre, 
                  ef.fecha_inicio, 
                  ef.estado
                  ")
        ->innerJoin('ep.ejecucion_flujo_trabajo', 'ftv', Expr\Join::WITH, 'ftv.id = ef.flujo_trabajo_version_id AND ftv.nombre LIKE :query')
        ->innerJoin('ef.flujo_trabajo_version', 'ftv', Expr\Join::WITH, 'ftv.id = ef.flujo_trabajo_version_id AND ftv.nombre LIKE :query')
        ->where("ef.radicado = :radicado")                             
        ->setParameter('query', "%" . $filter . "%")
        ->setParameter('radicado', $radicado)
        ->orderBy('ef.id', $order)
        ->getQuery();


        return $query->execute();
    }
}
