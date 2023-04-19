<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\RegistroCampo;
use App\Entity\Registro;
use App\Entity\CampoFormularioVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\ORM\Query\Expr;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class RegistroCampoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistroCampo::class);
    }

    public function findRelationedParent($em, $registroId, $campoFormularioVersionId)
    {
        $sql = "SELECT cfv.id FROM campo_formulario_version cfv
                INNER JOIN campo_formulario_version cfv2 ON cfv.campo_formulario_id = cfv2.campo_formulario_id
                WHERE cfv2.id = ?";

                
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $campoFormularioVersionId
        ];
        $stmt->execute($args);

        $ids = $stmt->fetchAll();

        $result = new ArrayCollection();

        if (count($ids) > 0) {
            $result = $this->createQueryBuilder("rc")
            ->select('rc')
            ->innerJoin('rc.campoFormularioVersion', 'cfv', Expr\Join::WITH, 'cfv.id = rc.campo_formulario_version_id')
            ->where('rc.campo_formulario_version_id IN (:ids)')
            ->andWhere('rc.registro_id = :registroId')
            ->andWhere('cfv.indice = 1 AND cfv.estado_id = 1')
            ->setParameter('ids', $ids)
            ->setParameter('registroId', $registroId)
            ->getQuery()
            ->execute();
        }

        return $result;
    }

    public function findRelationedChild($registroOrigenId,$queryString, $page, $itemsPerPage, $estado, $orderBy): Paginator
    {
        $querys  = $this->createQueryBuilder("rc")
            ->select('rc')
            ->innerJoin('rc.campoFormularioVersion', 'cfv', Expr\Join::WITH, 'cfv.id = rc.campo_formulario_version_id')
            ->innerJoin('App\\Entity\\Registro','r', Expr\Join::WITH,'r.id = rc.registro_id')
            ->andWhere('rc.registro_id_origen = :registroOrigenId')
            ->andWhere('cfv.estado_id = :estadoId')
            ->andWhere('r.nombre_formulario like :queryString')
            ->setParameter('registroOrigenId', $registroOrigenId)
            ->setParameter('estadoId', $estado)
            ->setParameter('queryString',  "%" . $queryString . "%")
            ->setFirstResult(($page - 1) * $itemsPerPage);

        if (null !== $orderBy) {
            $querys->orderBy('rc.id' , $orderBy);
        }

        $querys->setMaxResults($itemsPerPage)
        ->getQuery();

        $criteria = Criteria::create()
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);
        $querys->addCriteria($criteria);
        
        $doctrinePaginator = new DoctrinePaginator($querys);
        $doctrinePaginator->setUseOutputWalkers(false);
        $totalItems = count($doctrinePaginator);
        $result = new Paginator($doctrinePaginator);
        return $result;

    }
}
