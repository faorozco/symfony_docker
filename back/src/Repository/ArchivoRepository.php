<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Archivo;
use App\Entity\Formulario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Common\Collections\ArrayCollection;

class ArchivoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Archivo::class);
    }

    public function findFiles($em, $queryString, $page, $orderBy, $itemsPerPage, $registro, $user)
    {
        $sql = "SELECT id FROM (SELECT a.archivo_origen, max(a.version) as version, max(a.id) as id FROM archivo a
        WHERE a.registro_id = ? AND a.estado_id = 1 GROUP BY a.archivo_origen) a;";

        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $registro->getId()
        ];
        $stmt->execute($args);

        $ids = $stmt->fetchAll();

        $result = new ArrayCollection();

        if (count($ids)) {
            //Consultar los grupos que tiene un usuario tiene
            $userGroups = $user->getGrupos();
            foreach ($userGroups as $userGroup) {
                $userGroupIds[] = $userGroup->getId();
            }

            //Consultar los grupos que tiene asignado el formulario con el que se creo el registro.
            $formulario = $em->getRepository(Formulario::class)->findOneById($registro->getFormularioVersion()->getFormularioId());
            $formGroups = $formulario->getGrupos();
            $formGroupIds = array();
            foreach ($formGroups as $formGroup) {
                $formGroupIds[] = $formGroup->getId();
            }
            //Verificar si hay algun grupo aginado al usuario que lo tenga el registro
            // Si hay algún grupo del usuario que lo tenga el registro se ejecuta la consulta normal
            if (count(array_intersect($formGroupIds, $userGroupIds)) > 0) {
                $query = $this->createQueryBuilder("archivo");
                $query->where("archivo.nombre like :query");
                $query->andWhere("archivo.id IN (:ids)");
                $query->andWhere("archivo.estado_id = 1");
                $query->setParameter('query', "%" . $queryString . "%");
                $query->setParameter('ids', $ids);
                if (null !== $orderBy) {
                    $query->orderBy('archivo.' . key($orderBy), $orderBy[key($orderBy)]);
                }
                $query->setFirstResult(($page - 1) * $itemsPerPage);
                $query->setMaxResults($itemsPerPage);
                $query->getQuery();
            } else if (count(array_intersect($formGroupIds, $userGroupIds)) == 0) {
                $query = $this->createQueryBuilder("archivo");
                $query->where("archivo.estado_id = 1");
                $query->andWhere("archivo.tipo_documental = :tipo_documental");
                $query->andWhere("archivo.id IN (:ids)");
                $query->setParameter('tipo_documental', true);
                $query->setParameter('ids', $ids);
                $query->setFirstResult(($page - 1) * $itemsPerPage);
                $query->setMaxResults($itemsPerPage);
                $query->getQuery();
            }

            //Si no tiene ningun acceso mostrar solo el archivo marcado como tipo documental

            $doctrinePaginator = new DoctrinePaginator($query);
            //IMPORTANTE: Como usar Paginator con consultas Escalares
            $doctrinePaginator->setUseOutputWalkers(true);
            $result = new Paginator($doctrinePaginator);
        }

        return $result;
    }

    public function findFilesFlujo($em,$idFlujo)
    {
        $sql = "SELECT id FROM (SELECT a.archivo_origen, max(a.version) as version, max(a.id) as id FROM archivo a
        WHERE a.ejecucion_paso_id = ? AND a.estado_id = 1 GROUP BY a.archivo_origen) a;";

        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $idFlujo
        ];
        $stmt->execute($args);

        $ids = $stmt->fetchAll();

        if (count($ids)) {
            $query = $this->createQueryBuilder("archivo")
            ->Where("archivo.id IN (:ids)")
            ->andWhere("archivo.estado_id = 1")
            ->setParameter('ids', $ids)
            ->orderBy('archivo.fecha_version', 'ASC')
            ->getQuery();
            return $query->execute();
        }

        return [];
    }

    public function findLastVersion($archivoOrigen)
    {
        $archivo = $this->createQueryBuilder("a")
            ->where('a.archivo_origen  = :archivoOrigen')
            ->andWhere('a.estado_id  = :estado_id')
            ->setParameter('archivoOrigen', $archivoOrigen)
            ->setParameter('estado_id', 1)
            ->addOrderBy('a.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->execute();

        if (count($archivo) > 0) {
            return $archivo[0];
        } else {
            return null;
        }
    }
}
