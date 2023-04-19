<?php

namespace App\Repository;

use App\Entity\Notificacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class NotificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notificacion::class);
    }

    public function findToNotify()
    {
        $paraEnviar = $this->createQueryBuilder("n")
            ->where('n.notificado = false')
            ->addOrderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $paraEnviar;
    }

    public function getNotificationsByRegistro($registroId){
        $notifications = $this->createQueryBuilder("n")
        ->select("n.id")
        ->where('n.registro_id = :registroId')
        ->setParameter("registroId", $registroId)
        ->getQuery()
        ->getArrayResult();
        $notificationsIds=array();
        foreach($notifications as $notification){
            $notificationsIds[]=$notification["id"];
        }
    return $notificationsIds;
    }

}
