<?php

namespace App\Repository;

use App\Entity\Auditoria;
use App\Entity\CampoFormulario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class AuditoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auditoria::class);
    }

    public function getAuditoriaRegistro($em, $page, $query, $items_per_page, $registro_id): array
    {
        $result = array();
        //Consulto los registros relacionados con $registroId
        //Ahora consulto los campos a imprimir de la entidad
        $entityResults = $this->createQueryBuilder("a")
            ->where('a.entidad_id = :registro_id')
            ->andWhere("a.valor_actual like :query")
            ->setParameter('registro_id', $registro_id)
            ->setParameter('query', "%" . $query . "%")
            ->orderBy('a.id', "DESC")
            ->setFirstResult(($page - 1) * $items_per_page)
            ->setMaxResults($items_per_page)
            ->getQuery()
            ->execute();

        foreach ($entityResults as $entityResult) {
            $result[] = array(
                "id" => $entityResult->getId(),
                "entidad" => trim($entityResult->getEntidad()),
                "valorActual" => trim($entityResult->getValorActual()),
                "valorAnterior" => trim($entityResult->getValorAnterior()),
                "ipCliente" => trim($entityResult->getIpCliente()),
                "fecha" => $entityResult->getFecha()->format('Y-m-d H:i:s'),
            );
        }
        $resultado[] = $result;
        return $resultado;
    }

    public function getAuditoria($items_per_page, $request, $entidad): Paginator
    {
            $page = $request->query->get('page');
            $query = $request->query->get('query');
            $fecha = $request->query->get('fecha');
            $entidadId = $request->query->get('entidad_id');
            $user = $request->query->get('usuario');

            $result = array();
            $selectexpr =  'a.entidad_id AS entidadId,a.entidad,a.estado_id AS estadoId,a.fecha,a.id,a.operacion,a.username,a.usuario_id AS usuarioId,c.'.str_replace('-','',$entidad->getCampoBusqueda()).' AS campo,a.valor_actual AS valorActual,a.valor_anterior AS valorAnterior';
            //Consulto los registros relacionados con $registroId
            //Ahora consulto los campos a imprimir de la entidad
            $queryBuild = $this->createQueryBuilder("a")
                ->select($selectexpr)
                ->join('App\\Entity\\'.$entidad->getNombre(),'c', Expr\Join::WITH,'c.id = a.entidad_id')
                ->where('a.entidad = :entidad_nombre')
                ->setParameter('entidad_nombre', $entidad->getNombre());
        if (null !== $fecha){ 
               $queryBuild
                ->andWhere('a.fecha BETWEEN :after AND :before')
                ->setParameter('after', $fecha["after"])
                ->setParameter('before', $fecha["before"]);
        } 
        if($user !== null){
            $queryBuild 
            ->andWhere('a.usuario_id = :usuarioId')
            ->setParameter('usuarioId', $user);
        } 
        if($entidadId !== null){
                $queryBuild 
                ->andWhere('a.entidad_id = :entidad_id')
                ->setParameter('entidad_id', $entidadId);
        } 
        if($query !== null && $query !== "" ){
                $queryBuild 
                ->andWhere("c.campo like :query")
                ->setParameter('query', "%" . $query . "%");
            
        }
                $queryBuild
                ->orderBy('a.id', "DESC")
                ->setFirstResult(($page - 1) * $items_per_page)
                ->setMaxResults($items_per_page)
                ->getQuery();

            //Si no tiene ningun acceso mostrar solo el archivo marcado como tipo documental

            $doctrinePaginator = new DoctrinePaginator($queryBuild);
            //IMPORTANTE: Como usar Paginator con consultas Escalares
            $doctrinePaginator->setUseOutputWalkers(false);
            $result = new Paginator($doctrinePaginator);
            return $result;
    }
}
