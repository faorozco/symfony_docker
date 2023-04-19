<?php

namespace App\Repository;

use App\Entity\EstructuraDocumental;
use App\Utils\EntityUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

class EstructuraDocumentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstructuraDocumental::class);
    }

    public function findNonRelated2($em, $page, $query, $order_key, $order_orientation, $items_per_page, $relacionadosTRD)
    {
        $response = array();
        //Consultar qué elementos de la Estructura Documental todavia no estan relacionados con una TRD
        $serie = $_ENV["SERIE"] + $_ENV["SUBSECCION"] + $_ENV["SECCION"];
        $subSerie = $_ENV["SUBSERIE"] + $_ENV["SERIE"] + $_ENV["SUBSECCION"] + $_ENV["SECCION"];
        if (count($relacionadosTRD) > 0) {
            $result = $this->createQueryBuilder('ed')
                ->where('ed.estado_id like :estado_id')
                ->andWhere('((ed.id NOT IN (:relacionadosTRD) AND LENGTH(ed.codigo_directorio)>=:serie AND LENGTH(ed.codigo_directorio)<=:subSerie) OR ed.codigo_directorio = :directorioTipoDocumental)')
                ->andWhere('ed.descripcion LIKE :query')
                ->setParameter('estado_id', 1)
                ->setParameter('relacionadosTRD', $relacionadosTRD)
                ->setParameter('directorioTipoDocumental', 0)
                ->setParameter('serie', $serie)
                ->setParameter('subSerie', $subSerie)
                ->setParameter('query', "%" . $query . "%")
                ->orderBy('ed.' . $order_key[0], $order_orientation)
                ->setFirstResult(($page - 1) * $items_per_page)
                ->setMaxResults($items_per_page)
                ->getQuery()
                ->execute();
        } else if (count($relacionadosTRD) == 0) {
            $result = $this->createQueryBuilder('ed')
                ->where('ed.estado_id like :estado_id')
                ->andWhere('((LENGTH(ed.codigo_directorio)>=:serie AND LENGTH(ed.codigo_directorio)<=:subSerie) OR ed.codigo_directorio = :directorioTipoDocumental)')
                ->andWhere('ed.descripcion LIKE :query')
                ->setParameter('estado_id', 1)
                ->setParameter('directorioTipoDocumental', 0)
                ->setParameter('serie', $serie)
                ->setParameter('subSerie', $subSerie)
                ->setParameter('query', "%" . $query . "%")
                ->orderBy('ed.' . $order_key[0], $order_orientation)
                ->setFirstResult(($page - 1) * $items_per_page)
                ->setMaxResults($items_per_page)
                ->getQuery()
                ->execute();
        }
        foreach ($result as $item) {           
            //calculo el nombre del directorio padre
            $directorioPadre = $em->getRepository(EstructuraDocumental::class)->findOneBy(array("codigo_directorio" => $item->getCodigoDirectorioPadre()));
            $response[] = array(
                "id" => $item->getId(),
                "codigoDirectorio" => $item->getId(),
                "descripcion" => $item->getDescripcion() . " [ " . $item->getCodigoDirectorioPadre() . " - " . $directorioPadre->getDescripcion() . "]",
            );
        }
        //$doctrinePaginator = new DoctrinePaginator($query);
        //$paginator = new Paginator($doctrinePaginator);
        return $response;
        // return array("result" => array("totalItems" => count($estructuraDocumentalResults), "items" => $estructuraDocumentalResults));
    }

    public function updateParentDirectory($codigoDirectorioPadreAnterior, $codigoDirectorioPadreNuevo)
    {
        $result = $this->createQueryBuilder('ed')
            ->update()
            ->set('ed.codigo_directorio_padre', $codigoDirectorioPadreNuevo)
            ->where('ed.codigo_directorio_padre = :codigoDirectorioPadreAnterior')
            ->setParameter('codigoDirectorioPadreAnterior', $codigoDirectorioPadreAnterior)
            ->getQuery()
            ->execute();
        return $result;
    }

    public function getTiposDocumentales($em, $page, $q, $items_per_page, $estructuraDocumentalId)
    {
        $response = array();
        $result = $this->createQueryBuilder("ed")
            ->where('ed.type = :type')
            ->andWhere("ed.estado_id = :estado_id")
            ->andWhere("ed.formulario_id IS NULL OR ed.id = :estructuraDocumentalId")
            ->andWhere("ed.descripcion like :query")
            ->setParameter('type', "tipo_documental")
            ->setParameter('estado_id', 1)
            ->setParameter('estructuraDocumentalId', $estructuraDocumentalId)
            ->setParameter('query', "%" . $q . "%")
            ->orderBy('ed.descripcion', "ASC")
            ->setFirstResult(($page - 1) * $items_per_page)
            ->setMaxResults($items_per_page)
            ->getQuery()
            ->execute();
        foreach ($result as $item) {
            $ruta = EntityUtils::crearRutaEstructuraDocumental($em, "", $item);
            $response[] = array(
                "estructuraDocumentalId" => $item->getId(),
                "descripcion" => $ruta,
            );
        }
        return $response;
    }

    public function getTipoDocumentalById($em, $id)
    {
        $response = array();
        $result = $this->createQueryBuilder("ed")
            ->leftJoin('ed.formulario', 'f')
            ->addSelect('f')
            ->where('ed.id = :id')
            ->andWhere("ed.estado_id = :estado_id")
            ->setParameter('id', $id)
            ->setParameter('estado_id', 1)
            ->getQuery()
            ->execute();
        foreach ($result as $item) {
            $response[] = array(
                "estructuraDocumentalId" => $item->getId(),
                "descripcion" => EntityUtils::crearRutaEstructuraDocumental($em, "", $item),
            );
        }
        return $response;
    }

    public function getMaxVersion()
    {
        $result = $this->createQueryBuilder("ed")
            ->select('ed.version as versionActual')
            ->orderBy('ed.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->execute();
        return $result;
    }

    public function updateVersion($versionNueva)
    {
        $result = $this->createQueryBuilder('ed')
            ->update()
            ->set('ed.version', $versionNueva)
            ->set('ed.has_change', 0)
            ->set('ed.fecha_version', ':date')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->execute();
        return $result;
    }

    public function checkDuplicateCodigoDirectorio($request)
    {
        $result = array();
        $result = $this->createQueryBuilder("ed")
            ->where('ed.codigo_directorio = :codigo_directorio')
            ->andWhere("ed.codigo_directorio <> 0")
            ->andWhere("ed.estado_id = :estado_id")
            ->setParameter('codigo_directorio', $request->attributes->get("codigo_directorio"))
            ->setParameter('estado_id', 1)
            ->getQuery()
            ->execute();
        if (count($result) > 0 && ($request->attributes->get("esActualizacion") != $result[0]->getCodigoDirectorio()) ) {
            return (array("result" => array("response" => "existe")));
        } else if (count($result) == 0 || ($request->attributes->get("esActualizacion") == $result[0]->getCodigoDirectorio())) {
            return (array("result" => array("response" => "noexiste")));
        }
    }

    public function checkDuplicateCodigosDirectorio($codigos_directorio)
    {
        $result = array();
        $result = $this->createQueryBuilder("ed")
            ->where('ed.codigo_directorio IN (:codigos_directorio)')
            ->andWhere('ed.codigo_directorio <> :tipodocumental')
            ->andWhere("ed.estado_id = :estado_id")
            ->setParameter('codigos_directorio', $codigos_directorio)
            ->setParameter('tipodocumental', 0)
            ->setParameter('estado_id', 1)
            ->getQuery()
            ->execute();

        return count($result);
    }

    public function checkActiveChildNodes($codigoDirectorio)
    {
        $activeChildNodes = 0;
        $result = $this->createQueryBuilder("ed")
            ->where('ed.codigo_directorio_padre = :codigo_directorio')
            ->andWhere("ed.estado_id = :estado_id")
            ->setParameter('codigo_directorio', $codigoDirectorio)
            ->setParameter('estado_id', 1)
            ->getQuery()
            ->execute();
        foreach ($result as $childNode) {
            if ($childNode->getEstadoId() == 1) {
                $activeChildNodes++;
            }
        }
        return $activeChildNodes;
    }

    public function findNonRelated3($em, $query) {
        $estructuraDocumentales = $this->createQueryBuilder('ed')
        ->select('ed')
        ->where('ed.estado_id = 1')
        ->andWhere('ed.tablaRetencion IS NULL')
        ->andWhere('ed.descripcion LIKE :query')
        ->getQuery()
        ->setParameter('query', "%" . $query . "%")
        ->getArrayResult();
    
        return $estructuraDocumentales;
    }

    public function findNonRelated($em, $query, $estructuraDocumentalId) {
        $sql = "SELECT concat(CASE WHEN ed.codigo_directorio = 0 THEN '' WHEN CHAR_LENGTH(ed.codigo_directorio) > 0 THEN CONCAT(ed.codigo_directorio, '-') END, ed.descripcion, '/', CASE WHEN ed2.codigo_directorio = 0 THEN '' WHEN CHAR_LENGTH(ed2.codigo_directorio) > 0 THEN CONCAT(ed2.codigo_directorio, '-') END, ed2.descripcion) as ruta, ed.* FROM estructura_documental ed
        INNER JOIN estructura_documental ed2 ON ed2.codigo_directorio = ed.codigo_directorio_padre
        WHERE ed.estado_id = 1 AND (ed.id = ? OR ed.tabla_retencion_id IS NULL) AND ed2.estado_id = 1 AND ed.codigo_directorio_padre <> 0 AND ed2.codigo_directorio_padre <> 0 AND ed.descripcion LIKE ? ORDER BY ed.descripcion;";

        $stmt = $em->getConnection()->prepare($sql);

        $args = [
            $estructuraDocumentalId,
            "%". $query . "%"
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function findOneByIdWithRoute($em, $estructuraDocumentalId) {
        $sql = "SELECT concat(CASE WHEN ed.codigo_directorio = 0 THEN '' WHEN CHAR_LENGTH(ed.codigo_directorio) > 0 THEN CONCAT(ed.codigo_directorio, '-') END, ed.descripcion, '/', CASE WHEN ed2.codigo_directorio = 0 THEN '' WHEN CHAR_LENGTH(ed2.codigo_directorio) > 0 THEN CONCAT(ed2.codigo_directorio, '-') END, ed2.descripcion) as ruta, ed.* FROM estructura_documental ed
        INNER JOIN estructura_documental ed2 ON ed2.codigo_directorio = ed.codigo_directorio_padre
        WHERE ed.id = ?;";

        $stmt = $em->getConnection()->prepare($sql);

        $args = [
            $estructuraDocumentalId
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function hasChanges($em) {
        $sql = "SELECT CASE WHEN EXISTS 
        (
           SELECT ED.has_change FROM estructura_documental ED WHERE (ED.has_change = true OR ED.version IS NULL) AND ED.estado_id = true
           UNION 
           SELECT TR.has_change FROM tabla_retencion TR WHERE (TR.has_change = true OR TR.version IS NULL)  AND TR.estado_id = true
        )
        THEN 'true' ELSE 'false' END AS has_change;";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll()[0]['has_change'] == 'true';
    }

    public function markUpdated($id)
    {
        $result = $this->createQueryBuilder('ed')
            ->update()
            ->set('ed.has_change', true)
            ->where('ed.id = ?1')
            ->setParameter(1, $id)
            ->getQuery()
            ->execute();
        return $result;
    }

    public function estructuraDocumentalToVersion($em) {
        $sql = "SELECT ed.* FROM estructura_documental ed
                LEFT JOIN formulario f ON f.id = ed.formulario_id
                WHERE ed.estado_id = 1 AND (((ed.type IS NULL OR ed.type = '') AND ed.formulario_id IS NULL) OR f.estado_id = 1) ORDER BY ed.formulario_id DESC;";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        $ids = $stmt->fetchAll();

        $estructuraDocumentals = new ArrayCollection();

        if(count($ids)) {
            $estructuraDocumentals = $this->createQueryBuilder("ed")
            ->select('ed')
            ->where('ed.id IN (:ids)')
            ->orderBy('ed.formulario', 'DESC')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
        }

        return $estructuraDocumentals;
    }

    public function sincroniceTRD($em) {
        $sql = "UPDATE estructura_documental ed
                INNER JOIN tabla_retencion tr ON ed.id = tr.estructura_documental_id
                SET ed.tabla_retencion_id = tr.id
                WHERE ed.tabla_retencion_id IS NULL;";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
    }
}
