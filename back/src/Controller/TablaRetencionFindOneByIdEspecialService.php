<?php

namespace App\Controller;

use App\Entity\TablaRetencion;
use App\Dto\TablaRetencionDto;
use Doctrine\ORM\EntityManagerInterface;

class TablaRetencionFindOneByIdEspecialService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $login
     *
     * @return TablaRetencionDto
     */
    public function find($id): tablaRetencionDto
    {
        $tablaRetencion = $this->em->getRepository(TablaRetencion::class)
            ->findOneById($id);

        $tablaRetencionDto = new TablaRetencionDto();
        $tablaRetencionDto->setId($tablaRetencion->getId());
        $tablaRetencionDto->setVersion($tablaRetencion->getVersion());
        $tablaRetencionDto->setTiempoRetencionArchivoGestion($tablaRetencion->getTiempoRetencionArchivoGestion());
        $tablaRetencionDto->setUnidadRetencionArchivoGestion($tablaRetencion->getUnidadRetencionArchivoGestion());
        $tablaRetencionDto->setTiempoRetencionArchivoCentral($tablaRetencion->getTiempoRetencionArchivoCentral());
        $tablaRetencionDto->setUnidadRetencionArchivoCentral($tablaRetencion->getUnidadRetencionArchivoCentral());
        $tablaRetencionDto->setTipoSoporte($tablaRetencion->getTipoSoporte());
        $tablaRetencionDto->setDisposicionFinalBorrar($tablaRetencion->getDisposicionFinalBorrar());
        $tablaRetencionDto->setDisposicionFinalConservacionTotal($tablaRetencion->getDisposicionFinalConservacionTotal());
        $tablaRetencionDto->setDisposicionFinalConservacionDigital($tablaRetencion->getDisposicionFinalConservacionDigital());
        $tablaRetencionDto->setDisposicionFinalMicrofilmado($tablaRetencion->getDisposicionFinalMicrofilmado());
        $tablaRetencionDto->setDisposicionFinalSeleccion($tablaRetencion->getDisposicionFinalSeleccion());
        $tablaRetencionDto->setProcedimientoDisposicion($tablaRetencion->getProcedimientoDisposicion());
        $tablaRetencionDto->setModulo($tablaRetencion->getModulo());
        $tablaRetencionDto->setInicioVigencia($tablaRetencion->getInicioVigencia());
        //$tablaRetencionDto->setFinVigencia($tablaRetencion->getFinVigencia());
        $tablaRetencionDto->setEstructuraDocumentalId($tablaRetencion->getEstructuraDocumentalId());
        $tablaRetencionDto->setEstadoId($tablaRetencion->getEstadoId());
        $tablaRetencionDto->setTipoDocumentalId($tablaRetencion->getTipoDocumentalId());
        $tablaRetencionDto->setDescripcion($tablaRetencion->getEstructuraDocumental()->getDescripcion() . " - " . $tablaRetencion->getTipoDocumental()->getDescripcion());
        $tablaRetencionDto->setCodigoArchivoDocumental($tablaRetencion->getEstructuraDocumental()->getId() . "-" . $tablaRetencion->getTipoDocumental()->getId());
        $tablaRetencionDto->setTransferenciaMedioElectronico($tablaRetencion->getTransferenciaMedioElectronico());
        $tablaRetencionDto->setDireccionDocumentosAlmacenadosElectronicamente($tablaRetencion->getDireccionDocumentosAlmacenadosElectronicamente());
        if (null !== $tablaRetencion->getValordocumentals()) {
            $valordocumentals = array();
            foreach ($tablaRetencion->getValordocumentals() as $valordocumental) {
                if ($valordocumental->getEstadoId() == 1) {
                    switch ($valordocumental->getTipo()) {
                        case 1:$valordocumentals["primario"][] = array(
                                "id" => $valordocumental->getId(),
                                "descripcion" => $valordocumental->getDescripcion(),
                            );
                            break;
                        case 2:$valordocumentals["atributo"][] = array(
                                "id" => $valordocumental->getId(),
                                "descripcion" => $valordocumental->getDescripcion(),
                            );
                            break;
                        default:break;

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
}
