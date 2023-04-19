<?php

namespace App\Repository;

use App\Entity\EjecucionPaso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\AST\Functions\Numeric\TimestampDiff;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class EjecucionPasoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EjecucionPaso::class);
    }
//Query por radicado
    public function buscarFlujoRadicado($radicado, $filter, $order, $usuarioId, $page, $size)
    {
        if($order === 'asc') {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        $query = $this->getShowColumns()
        ->innerJoin('ep.ejecucionFlujo', 'ef', Expr\Join::WITH, 'ef.id = ep.ejecucion_flujo_id')
        ->innerJoin('ep.pasoVersion', 'pv', Expr\Join::WITH, 'pv.id = ep.paso_version_id')
        ->innerJoin('ep.usuarioResponsable', 'u', Expr\Join::WITH, 'u.id = ep.usuario_responsable_id')
        ->innerJoin('ep.usuarioRemitente', 'ur', Expr\Join::WITH, 'ur.id = ep.usuario_remitente_id')
        ->innerJoin('ef.flujo_trabajo_version', 'ftv', Expr\Join::WITH, 'ftv.id = ef.flujo_trabajo_version_id AND (ftv.nombre LIKE :query OR pv.descripcion  LIKE :query)')
        ->where("ef.radicado = :radicado")                             
        ->andWhere("ep.ejecucion_paso_id_siguiente IS NULL OR ep.devolucion IS NOT NULL")
        ->andWhere("ef.estado <> 'CANCELADO'")  
        //->andWhere("ep.usuario_responsable_id = :usuarioId OR ep.usuario_responsable_visto_bueno_id = :usuarioId OR ep.estado IN ('INTERRUMPIDO', 'COMPLETADO')")                          
        ->setParameter('query', "%" . $filter . "%")
        ->setParameter('radicado', $radicado)
        //->setParameter('usuarioId', $usuarioId)
        ->setFirstResult(($page - 1) * $size)
        ->orderBy('ef.id', $order)
        ->setMaxResults($size)
        ->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findUserResponsibleWorkload($em, $groupId) {
        $sql = 'SELECT u.id, u.steps FROM (SELECT u.id, 0 AS steps FROM usuario u
            INNER JOIN usuario_grupo ug ON ug.grupo_id = ? AND u.id IN (ug.usuario_id)
            WHERE u.id NOT IN (SELECT ep.usuario_responsable_id FROM ejecucion_paso ep WHERE ep.estado = "ACTIVO")
            UNION
            SELECT u.id, COUNT(*) FROM usuario u
            INNER JOIN usuario_grupo ug ON ug.grupo_id = ?
            INNER JOIN ejecucion_paso ep ON u.id = ep.usuario_responsable_id AND ep.estado = "ACTIVO"
            WHERE u.id IN (ug.usuario_id) GROUP BY u.id) u ORDER BY u.steps';
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $groupId,
            $groupId
        ];
        $stmt->execute($args);
        // returns an array of arrays (i.e. a raw data set)
        $results = $stmt->fetchAll();
        return $results[0]["id"];
    }
//query tareas 
    public function buscarFlujoPorUsuario($usuarioId, $filter, $order, $page, $size)
    {
        if($order === 'asc') {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        $query = $this->getShowColumns()
        ->innerJoin('ep.ejecucionFlujo', 'ef', Expr\Join::WITH, 'ef.id = ep.ejecucion_flujo_id')
        ->innerJoin('ep.pasoVersion', 'pv', Expr\Join::WITH, 'pv.id = ep.paso_version_id')
        ->innerJoin('ep.usuarioResponsable', 'u', Expr\Join::WITH, 'u.id = ep.usuario_responsable_id')
        ->innerJoin('ep.usuarioRemitente', 'ur', Expr\Join::WITH, 'ur.id = ep.usuario_remitente_id')
        ->innerJoin('ef.flujo_trabajo_version', 'ftv', Expr\Join::WITH, 'ftv.id = ef.flujo_trabajo_version_id AND (ftv.nombre LIKE :query OR pv.descripcion  LIKE :query)')
        ->where("ep.usuario_responsable_id = :usuarioId OR (ep.usuario_responsable_visto_bueno_id = :usuarioId AND ep.estado = 'PENDIENTE_VISTO_BUENO')")                             
        ->andWhere("ep.ejecucion_paso_id_siguiente IS NULL OR ep.devolucion IS NOT NULL")  
        ->andWhere("ep.estado IN ('ACTIVO', 'PENDIENTE_VISTO_BUENO')") 
        ->andWhere("ef.estado <> 'CANCELADO'")                          
        ->setParameter('query', "%" . $filter . "%")
        ->setParameter('usuarioId', $usuarioId)
        ->setFirstResult(($page - 1) * $size)
        ->orderBy('ef.id', $order)
        ->setMaxResults($size)
        ->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function summaryStep($em, $ejecucionFlujoId) {
        $sql = "SELECT ep.id as id, numero, descripcion as pasoNombre, u.login as usuario, estado, fecha_inicio as fechaInicio, fecha_fin as fechaFin, comment, file
                FROM ejecucion_paso ep
                INNER JOIN paso_version pv ON ep.paso_version_id = pv.id
                INNER JOIN usuario u ON u.id = ep.usuario_responsable_id
                WHERE ejecucion_flujo_id = ?
                ORDER BY numero DESC;";

        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $ejecucionFlujoId
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function totalSteps($em, $ejecucionFlujoId) {
        $sql = "SELECT count(*) as totalSteps FROM paso_version pv
                INNER JOIN ejecucion_paso ep ON ep.paso_version_id = pv.id
                WHERE ep.ejecucion_flujo_id = ?";

        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $ejecucionFlujoId
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function buscarFlujoPorId($ejecucionFlujoId)
    {
        $query = $this->getShowColumns()
        ->innerJoin('ep.ejecucionFlujo', 'ef', Expr\Join::WITH, 'ef.id = ep.ejecucion_flujo_id')
        ->innerJoin('ep.pasoVersion', 'pv', Expr\Join::WITH, 'pv.id = ep.paso_version_id')
        ->innerJoin('ep.usuarioResponsable', 'u', Expr\Join::WITH, 'u.id = ep.usuario_responsable_id')
        ->innerJoin('ep.usuarioRemitente', 'ur', Expr\Join::WITH, 'ur.id = ep.usuario_remitente_id')
        ->innerJoin('ef.flujo_trabajo_version', 'ftv', Expr\Join::WITH, 'ftv.id = ef.flujo_trabajo_version_id')                            
        ->andWhere("ep.ejecucion_paso_id_siguiente IS NULL")  
        ->andWhere("ef.id = :ejecucionFlujoId") 
        ->andWhere("ef.estado <> 'CANCELADO'")   
        ->setParameter('ejecucionFlujoId', $ejecucionFlujoId)
        ->getQuery();

        return $query->execute();
    }

    private function getShowColumns() {
        return $this->createQueryBuilder("ep")
        ->select("ef.id, 
                    ef.flujo_trabajo_version_id as flujoTrabajoVersionId, 
                    ef.radicado as radicado,
                    ftv.nombre,
                    ef.estado as estadoFlujo,
                    DATE_FORMAT(ef.fecha_inicio, '%d-%m-%Y %h:%i %p') as flujoFechaInicio,
                    DATE_FORMAT(ef.fecha_fin, '%d-%m-%Y %h:%i %p') as flujoFechaFin,
                    ftv.descripcion as flujoDescripcion,
                    ep.file,
                    ep.comment,
                    ep.id as ejecucionPasoId,
                    DATE_FORMAT(ep.fecha_inicio, '%d-%m-%Y %h:%i %p') as fechaInicio, 
                    DATE_FORMAT(ep.fecha_fin, '%d-%m-%Y %h:%i %p') as fechaFin, 
                    ep.estado,
                    pv.id as pasoVersionId,
                    pv.numero,
                    pv.descripcion as pasoNombre,
                    u.login as usuario,
                    u.id as usuarioId,
                    ep.fill_form as ejecucionPasoFillForm,
                    ep.ejecucion_flujo_id_iniciado as ejecucionPasoFlujoIniciadoId,
                    ep.usuario_responsable_visto_bueno_id as usuarioResponsableVistoBuenoId,
                    DATE_FORMAT(ep.fecha_vencimiento, '%d-%m-%Y %h:%i %p') as fechaVencimiento,
                    TimestampDiff(MINUTE, CURRENT_TIMESTAMP(), ep.fecha_vencimiento) as vigencia,
                    CONCAT(u.nombre1, ' ', u.nombre2) as responsableNombre,
                    CONCAT(ur.nombre1, ' ', ur.nombre2) as remitenteNombre,
                    ur.login as remitente");
    }
}
