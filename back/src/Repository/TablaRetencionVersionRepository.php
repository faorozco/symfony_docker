<?php

namespace App\Repository;

use App\Entity\TablaRetencionVersion;
use App\Utils\EntityUtils;
use App\Dto\TablaRetencionDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TablaRetencionVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TablaRetencionVersion::class);
    }

    public function FindSpecialGet($em, $page, $query, $order_key, $order_orientation, $items_per_page): array
    {
        $tablasRetencionStandard = array();
        $isInactiveOnly = explode("|", $query);
        if (isset($isInactiveOnly[1])) {
            $tablaRetencionResults = $this->createQueryBuilder('tr')
                ->leftJoin("tr.estructuraDocumentalVersion", "ed")
                ->where('tr.estado_id = :estado_id')
                ->setParameter('estado_id', 0)
                ->orderBy('tr.' . $order_key[0], $order_orientation)
                // ->setFirstResult(($page - 1) * $items_per_page)
                // ->setMaxResults($items_per_page)
                ->getQuery()
                ->execute();
        } else {
            $tablaRetencionResults = $this->createQueryBuilder('tr')
                ->leftJoin("tr.estructuraDocumentalVersion", "ed")
                ->where('tr.estado_id = :estado_id')
                ->setParameter('estado_id', 1)
                ->orderBy('tr.' . $order_key[0], $order_orientation)
                ->getQuery()
                ->execute();
        }
        $query = $isInactiveOnly[0];

        foreach ($tablaRetencionResults as $tablaRetencionRow) {
            $ruta = implode(" > ", array_reverse(explode("/", mb_strtoupper(EntityUtils::crearRutaEstructuraDocumental($em, "", $tablaRetencionRow->getEstructuraDocumentalVersion())))));

            $stringToFilter = strtolower($tablaRetencionRow->getEstructuraDocumentalVersion()->getDescripcion() . " " . EntityUtils::crearRutaEstructuraDocumental($em, "", $tablaRetencionRow->getEstructuraDocumentalVersion()));
            //echo $ruta." / ".$query."\n";
            if (!empty($query)) {
                if (strpos($stringToFilter, strtolower($query)) !== false) {
                    $tablasRetencionStandard[] = self::agregarElementoTRD($tablaRetencionRow, $ruta);
                }
            } else {
                $tablasRetencionStandard[] = self::agregarElementoTRD($tablaRetencionRow, $ruta);
            }
        }
        return array("result" => array("totalItems" => count($tablasRetencionStandard), "items" => array_slice($tablasRetencionStandard, ($page - 1) * $items_per_page, $items_per_page)));
    }

    protected function agregarElementoTRD($tablaRetencionRow, $ruta)
    {
        $tablaRetencionDto = new TablaRetencionDto();
        $tablaRetencionDto->setId($tablaRetencionRow->getId());
        $tablaRetencionDto->setVersion($tablaRetencionRow->getVersion());
        if (null !== $tablaRetencionRow->getTiempoRetencionArchivoGestion()) {
            $tablaRetencionDto->setTiempoRetencionArchivoGestion($tablaRetencionRow->getTiempoRetencionArchivoGestion());
        }

        if (null !== $tablaRetencionRow->getUnidadRetencionArchivoGestion()) {
            $tablaRetencionDto->setUnidadRetencionArchivoGestion($tablaRetencionRow->getUnidadRetencionArchivoGestion());
        }

        if (null !== $tablaRetencionRow->getTiempoRetencionArchivoCentral()) {
            $tablaRetencionDto->setTiempoRetencionArchivoCentral($tablaRetencionRow->getTiempoRetencionArchivoCentral());
        }

        if (null !== $tablaRetencionRow->getUnidadRetencionArchivoCentral()) {
            $tablaRetencionDto->setUnidadRetencionArchivoCentral($tablaRetencionRow->getUnidadRetencionArchivoCentral());
        }

        $tablaRetencionDto->setTipoSoporte($tablaRetencionRow->getTipoSoporte());
        $tablaRetencionDto->setDisposicionFinalBorrar($tablaRetencionRow->getDisposicionFinalBorrar());
        $tablaRetencionDto->setDisposicionFinalConservacionDigital($tablaRetencionRow->getDisposicionFinalConservacionDigital());
        $tablaRetencionDto->setDisposicionFinalDigitalizacionMicrofilmacion($tablaRetencionRow->getDisposicionFinalDigitalizacionMicrofilmacion());
        $tablaRetencionDto->setDisposicionFinalMigrar($tablaRetencionRow->getDisposicionFinalMigrar());
        $tablaRetencionDto->setDisposicionFinalConservacionTotal($tablaRetencionRow->getDisposicionFinalConservacionTotal());
        $tablaRetencionDto->setDisposicionFinalMicrofilmado($tablaRetencionRow->getDisposicionFinalMicrofilmado());
        $tablaRetencionDto->setDisposicionFinalSeleccion($tablaRetencionRow->getDisposicionFinalSeleccion());
        $tablaRetencionDto->setProcedimientoDisposicion($tablaRetencionRow->getProcedimientoDisposicion());
        $tablaRetencionDto->setLeyNormatividad($tablaRetencionRow->getLeyNormatividad());
        $tablaRetencionDto->setModulo($tablaRetencionRow->getModulo());
        $tablaRetencionDto->setInicioVigencia($tablaRetencionRow->getInicioVigencia());
        // $tablaRetencionDto->setFinVigencia($tablaRetencionRow->getFinVigencia());
        $tablaRetencionDto->setEstructuraDocumentalId($tablaRetencionRow->getEstructuraDocumentalVersionId());
        $tablaRetencionDto->setEstadoId($tablaRetencionRow->getEstadoId());
        //$tablaRetencionDto->setTipoDocumentalId($tablaRetencionRow->getTipoDocumentalId());
        $tablaRetencionDto->setDescripcion($ruta);
        $tablaRetencionDto->setTransferenciaMedioElectronico($tablaRetencionRow->getTransferenciaMedioElectronico());
        $tablaRetencionDto->setDireccionDocumentosAlmacenadosElectronicamente($tablaRetencionRow->getDireccionDocumentosAlmacenadosElectronicamente());
        if ($tablaRetencionRow->getEstructuraDocumentalVersion()->getCodigoDirectorio() != 0)
            $tablaRetencionDto->setCodigoArchivoDocumental($tablaRetencionRow->getEstructuraDocumentalVersion()->getCodigoDirectorioPadre() . "-" . $tablaRetencionRow->getEstructuraDocumentalVersion()->getCodigoDirectorio());
        else
            $tablaRetencionDto->setCodigoArchivoDocumental($tablaRetencionRow->getEstructuraDocumentalVersion()->getCodigoDirectorioPadre());
        /*$tieneFormularioRelacionado = $tablaRetencionRow->getFormulario();
        if (null !== $tieneFormularioRelacionado) {
            $tablaRetencionDto->setTieneFormulario(true);
            $tablaRetencionDto->setFormularioId($tieneFormularioRelacionado->getId());
        } else if (null === $tieneFormularioRelacionado) {
            $tablaRetencionDto->setTieneFormulario(false);
        }*/
        if (null !== $tablaRetencionRow->getValordocumentals()) {
            $valordocumentals = array();
            foreach ($tablaRetencionRow->getValordocumentals() as $valordocumental) {
                if ($valordocumental->getEstadoId() == 1) {
                    switch ($valordocumental->getTipo()) {
                        case 1:
                            $valordocumentals["primario"][] = array(
                                "id" => $valordocumental->getId(),
                                "descripcion" => $valordocumental->getDescripcion(),
                            );
                            break;
                        case 2:
                            $valordocumentals["atributo"][] = array(
                                "id" => $valordocumental->getId(),
                                "descripcion" => $valordocumental->getDescripcion(),
                            );
                            break;
                        default:
                            break;
                    }
                }
            }
            if (!empty($valordocumentals["primario"])) {
                // Obtener una lista de columnas
                foreach ($valordocumentals["primario"] as $clave => $fila) {
                    $nombre[$clave] = $fila['descripcion'];
                }
                array_multisort($nombre, SORT_ASC, $valordocumentals["primario"]);
            }
            if (!empty($valordocumentals["atributo"])) {
                // Obtener una lista de columnas
                foreach ($valordocumentals["atributo"] as $clave => $fila) {
                    $nombre[$clave] = $fila['descripcion'];
                }
                array_multisort($nombre, SORT_ASC, $valordocumentals["atributo"]);
            }

            $tablaRetencionDto->setValorDocumental($valordocumentals);
        }
        return $tablaRetencionDto;
    }

    public function updateVersion($versionNueva)
    {
        $result = $this->createQueryBuilder('tr')
            ->update()
            ->set('tr.version', $versionNueva)
            ->set('tr.has_change', 0)
            ->set('tr.fecha_version', ':date')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->execute();
        return $result;
    }

    public function checkDuplicateIdsEstructuraDocumental($ids)
    {
        $result = array();
        $result = $this->createQueryBuilder("ed")
            ->where('ed.id IN (:ids)')
            ->andWhere("ed.estado_id = :estado_id")
            ->setParameter('ids', $ids)
            ->setParameter('estado_id', 1)
            ->getQuery()
            ->execute();
        return count($result);
    }

    public function markUpdated($id)
    {
        $result = $this->createQueryBuilder('tr')
            ->update()
            ->set('tr.has_change', true)
            ->where('tr.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->execute();
        return $result;
    }
}
