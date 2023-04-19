<?php

namespace App\Repository;

use App\Entity\EstructuraDocumentalVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class EstructuraDocumentalVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstructuraDocumentalVersion::class);
    }

    public function getVersions()
    {
        $result = $this->createQueryBuilder('edv')
            ->select('edv.version, edv.fecha_version')
            ->distinct()
            ->where('edv.version != 0')
            ->orderBy('edv.version', "ASC")
            ->getQuery()
            ->execute();
        return $result;
    }

    public function getEstructuraDocumentalVersionMaxVersionByEstructuraDocumentalId($formularioId)
    {
        $result = $this->createQueryBuilder('edv')
            ->select('edv')
            ->where('edv.formulario = :formularioId')
            ->andWhere('edv.estado_id = 1')
            ->orderBy('edv.version', "ASC")
            ->setMaxResults(1)
            ->setParameter('formularioId', $formularioId)
            ->getQuery()
            ->execute();

        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function getNode($node) {
        $result = $this->createQueryBuilder('edv')
            ->select('edv')
            ->where('edv.codigo_directorio = :node')
            ->andWhere('edv.estado_id = 1')
            ->orderBy('edv.version', "DESC")
            ->setMaxResults(1)
            ->setParameter('node', $node)
            ->getQuery()
            ->execute();
        return $result[0];
    }

    public function checkActiveChildNodes($codigoDirectorio, $version)
    {
        $result = $this->createQueryBuilder("edv")
            ->where('edv.codigo_directorio_padre = :codigo_directorio')
            ->andWhere("edv.estado_id = :estado_id")
            ->andWhere("edv.version = :version")
            ->setParameter('codigo_directorio', $codigoDirectorio)
            ->setParameter('estado_id', 1)
            ->setParameter('version', $version)
            ->getQuery()
            ->execute();

        return count($result);
    }

}
