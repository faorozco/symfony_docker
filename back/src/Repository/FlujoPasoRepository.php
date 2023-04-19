<?php

namespace App\Repository;

use App\Entity\Paso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FlujoPasoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paso::class);
    }

public function findFlujo($flujo_trabajo_id)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, 
                  u.flujo_trabajo_id, 
                  u.prioridad, 
                  u.descripcion, 
                  u.estado_id, 
                  u.plazo, 
                  u.time, 
                  u.numero
                  ")
        ->where("u.flujo_trabajo_id = :flujoTrabajoId")  
        ->andWhere('u.estado_id = 1')                           
        ->setParameter('flujoTrabajoId', $flujo_trabajo_id)
        ->orderBy('u.numero', 'ASC')
        ->getQuery();


        return $query->execute();
    }
}
