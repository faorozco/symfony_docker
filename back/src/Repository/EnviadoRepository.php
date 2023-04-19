<?php

namespace App\Repository;

use App\Entity\Enviado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class EnviadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enviado::class);
    }

    public function findToSend()
    {
        $paraEnviar = $this->createQueryBuilder("e")
            ->where('e.fecha_enviado IS NULL')
            ->andWhere('e.estado_id = 1')
            ->addOrderBy('e.id', 'ASC')
            ->setMaxResults($_ENV["MAX_SEND_MAILS"])
            ->getQuery()
            ->execute();

        return $paraEnviar;
    }

}
