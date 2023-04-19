<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\EstructuraDocumentalVersion;
use App\Entity\FormularioVersion;
use App\Entity\TablaRetencion;
use App\Entity\TablaRetencionVersion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class GenerarVersionService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function generate(Request $request)
    {
        $versionActual = 0;
        $versionNueva = 0;
        $fechaVersion = new \DateTime();
        /*
        Crear una entidad que se llame estructura_documental_version con los mismos campos que estructura_documental
        Crear una entidad que se llame tabla_retencion_version con los mismos campos que tabla_retencion
         */
        /*
        Verificar que hayan cambios que registrar en el cuadro de clasificación documental
        Esto se da si hay tuplas con el atributo version= NULL
         */
        $hasChanges = $this->em->getRepository(EstructuraDocumental::class)->hasChanges($this->em);

        if ($hasChanges) {
            /*
            Consultar versión de tabla de retención.
            Esto se hace sacando el mayor numero que haya en EstructuraDocumental
             */
            $versionActualTRD = $this->em->getRepository(EstructuraDocumental::class)->getMaxVersion();
            if ($versionActualTRD[0]["versionActual"] == null) {
                $versionActual = 0;
                $versionNueva = 1;
            } else {
                $versionActual = $versionActualTRD[0]["versionActual"];
                $versionNueva = $versionActualTRD[0]["versionActual"] + 1;

            }
            /*
            Se procede a consultar todo el Cuadro de Clasificación y se guarda en la entidad EstructuraDocumentalVersion con la versión actual
             */
            /*
            Tener en cuenta que solo debe pasar al versionado lo que este en la versión actual, es decir, nada que este en NULL(solo pasaran las de NULL cuando la versión de sea null, eso significa que es la primera versión de TRD y Cuadro de Clasificiación), eso pertenecerá a la nueva versión
             */

            $listEstructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)
                ->estructuraDocumentalToVersion($this->em);

            $arrayDocumentalType = new ArrayCollection();
            $hashParents = array();

            foreach($listEstructuraDocumental as $estructuraDocumental) {
                if($estructuraDocumental->getType() == 'tipo_documental' && $estructuraDocumental->getFormularioId() != null && $estructuraDocumental->getFormularioId() && $estructuraDocumental->getTablaRetencion() != null) {
                    $arrayDocumentalType[] = $estructuraDocumental;
                } else {
                    $hashParents[$estructuraDocumental->getCodigoDirectorio()] = $estructuraDocumental;
                }
            }

            $hashSeledtedParents = new ArrayCollection();
            $listToVersion = new ArrayCollection();

            $success = "";
            if (count($arrayDocumentalType) > 0) {
                foreach($arrayDocumentalType as $documentalType) {
                    $success = $this->findTreeDocumentalEstructure($documentalType, $hashParents, $listToVersion, $hashSeledtedParents, true);
    
                    if($success != "") {
                        break;
                    }
                }
            } else {
                $success = "No hay tipos documentales que tenga asociada la TRD y un formulario";
            }

            if($success == "") {
                $listEstructuraDocumentalWithFormulario = new ArrayCollection();
                foreach ($listToVersion as $estructuraDocumental) {
                    $estructuraDocumentalVersion = new EstructuraDocumentalVersion();
                    $estructuraDocumentalVersion->setCodigoDirectorioPadre($estructuraDocumental->getCodigoDirectorioPadre());
                    $estructuraDocumentalVersion->setPeso($estructuraDocumental->getPeso());
                    $estructuraDocumentalVersion->setCodigoDirectorio($estructuraDocumental->getCodigoDirectorio());
                    $estructuraDocumentalVersion->setDescripcion($estructuraDocumental->getDescripcion());
                    $estructuraDocumentalVersion->setIdestructura($estructuraDocumental->getIdestructura());
                    $estructuraDocumentalVersion->setEstadoId($estructuraDocumental->getEstadoId());
                    $estructuraDocumentalVersion->setType($estructuraDocumental->getType());
                    $estructuraDocumentalVersion->setVersion($versionNueva);
                    $estructuraDocumentalVersion->setFechaVersion($fechaVersion);
                    $estructuraDocumentalVersion->setEstructuraDocumental($estructuraDocumental);
                    $estructuraDocumentalVersion->setFormulario($estructuraDocumental->getFormulario());
                    
                    $this->em->persist($estructuraDocumentalVersion);

                    if($estructuraDocumental->getFormulario() != null) {
                        $listEstructuraDocumentalWithFormulario[] = $estructuraDocumentalVersion;
                    }
    
                    $tablaRetencion = $this->em->getRepository(TablaRetencion::class)->findOneBy(array("estructura_documental_id" => $estructuraDocumental->getId()));
    
                    if($tablaRetencion != null) {
                        $tablaRetencionVersion = new TablaRetencionVersion();
                        $tablaRetencionVersion->setEstructuraDocumentalVersion($estructuraDocumentalVersion);
                        $tablaRetencionVersion->setTablaRetencionId($tablaRetencion->getId());
                        $tablaRetencionVersion->setVersion($versionNueva);
                        $tablaRetencionVersion->setTiempoRetencionArchivoGestion($tablaRetencion->getTiempoRetencionArchivoGestion());
                        $tablaRetencionVersion->setUnidadRetencionArchivoGestion($tablaRetencion->getUnidadRetencionArchivoGestion());
                        $tablaRetencionVersion->setTiempoRetencionArchivoCentral($tablaRetencion->getTiempoRetencionArchivoCentral());
                        $tablaRetencionVersion->setUnidadRetencionArchivoCentral($tablaRetencion->getUnidadRetencionArchivoCentral());
                        $tablaRetencionVersion->setTipoSoporte($tablaRetencion->getTipoSoporte());
                        $tablaRetencionVersion->setDisposicionFinalBorrar($tablaRetencion->getDisposicionFinalBorrar());
                        $tablaRetencionVersion->setDisposicionFinalConservacionTotal($tablaRetencion->getDisposicionFinalConservacionTotal());
                        $tablaRetencionVersion->setDisposicionFinalConservacionTotal($tablaRetencion->getDisposicionFinalConservacionTotal());
                        $tablaRetencionVersion->setDisposicionFinalConservacionDigital($tablaRetencion->getDisposicionFinalConservacionDigital());
                        $tablaRetencionVersion->setDisposicionFinalMicrofilmado($tablaRetencion->getDisposicionFinalMicrofilmado());
                        $tablaRetencionVersion->setDisposicionFinalSeleccion($tablaRetencion->getDisposicionFinalSeleccion());
                        $tablaRetencionVersion->setDisposicionFinalMigrar($tablaRetencion->getDisposicionFinalMigrar());
                        $tablaRetencionVersion->setDisposicionFinalDigitalizacionMicrofilmacion($tablaRetencion->getDisposicionFinalDigitalizacionMicrofilmacion());
                        $tablaRetencionVersion->setProcedimientoDisposicion($tablaRetencion->getProcedimientoDisposicion());
                        $tablaRetencionVersion->setLeyNormatividad($tablaRetencion->getLeyNormatividad());
                        $tablaRetencionVersion->setModulo($tablaRetencion->getModulo());
                        $tablaRetencionVersion->setInicioVigencia($tablaRetencion->getInicioVigencia());
                        $tablaRetencionVersion->setEstadoId($tablaRetencion->getEstadoId());
                        $tablaRetencionVersion->setTransferenciaMedioElectronico($tablaRetencion->getTransferenciaMedioElectronico());
                        $tablaRetencionVersion->setTransferenciaMedioElectronico($tablaRetencion->getTransferenciaMedioElectronico());
                        $tablaRetencionVersion->setDireccionDocumentosAlmacenadosElectronicamente($tablaRetencion->getDireccionDocumentosAlmacenadosElectronicamente());
                        $tablaRetencionVersion->setFechaVersion($fechaVersion);
                        $this->em->persist($tablaRetencionVersion);
                    }
                }
                $this->em->flush();
                $this->em->getRepository(EstructuraDocumental::class)->updateVersion($versionNueva);
                $this->em->getRepository(TablaRetencion::class)->updateVersion($versionNueva);

                foreach ($listEstructuraDocumentalWithFormulario as $estructuraDocumentalVersion) {
                    $formularioVersionResult = $this->em->getRepository(FormularioVersion::class)->findBy(array("formulario_id" => $estructuraDocumentalVersion->getFormulario()->getId()), array("version" => "DESC"), 1);
                    if (count($formularioVersionResult) == 1) {
                        $formularioVersion = $formularioVersionResult[0];
                        $formularioVersion->setEstructuraDocumentalVersion($estructuraDocumentalVersion);
                        $this->em->persist($formularioVersion);
                    }
                }
                $this->em->flush();
                return array("result" => array("response" => "Se ha generado existosamente la versión " . $versionNueva));
            } else {
                return array("result" => array("response" => $success));
            }            
            /*
        Se procede a consultar toda la TRD y se guarda en la entidad EstructuraDocumentalVersion con la versión actual
         */
        } else {
            return array("result" => array("response" => "No hay nada para versionar"));
        }
        /*
     */
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function generate2(Request $request)
    {
        $versionActual = 0;
        $versionNueva = 0;
        $fechaVersion = new \DateTime();
        /*
        Crear una entidad que se llame estructura_documental_version con los mismos campos que estructura_documental
        Crear una entidad que se llame tabla_retencion_version con los mismos campos que tabla_retencion
         */
        /*
        Verificar que hayan cambios que registrar en el cuadro de clasificación documental
        Esto se da si hay tuplas con el atributo version= NULL
         */
        $hashChanges = $this->em->getRepository(EstructuraDocumental::class)->findBy(array("version" => null));

        if (count($hashChanges) > 0) {
            /*
            Consultar versión de tabla de retención.
            Esto se hace sacando el mayor numero que haya en EstructuraDocumental
             */
            $versionActualTRD = $this->em->getRepository(EstructuraDocumental::class)->getMaxVersion();
            if ($versionActualTRD[0]["versionActual"] == null) {
                $versionActual = 0;
                $versionNueva = 1;
            } else {
                $versionActual = $versionActualTRD[0]["versionActual"];
                $versionNueva = $versionActualTRD[0]["versionActual"] + 1;

            }
            /*
            Se procede a consultar todo el Cuadro de Clasificación y se guarda en la entidad EstructuraDocumentalVersion con la versión actual
             */
            /*
            Tener en cuenta que solo debe pasar al versionado lo que este en la versión actual, es decir, nada que este en NULL(solo pasaran las de NULL cuando la versión de sea null, eso significa que es la primera versión de TRD y Cuadro de Clasificiación), eso pertenecerá a la nueva versión
             */

            $listEstructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findAll();
            foreach ($listEstructuraDocumental as $estructuraDocumental) {
                if ($versionActualTRD[0]["versionActual"] == null || ($estructuraDocumental->getEstadoId() != 0 && $estructuraDocumental->getVersion() != null) || ($estructuraDocumental->getEstadoId() == 0 && $estructuraDocumental->getVersion() == null)) {
                    $estructuraDocumentalVersion = new EstructuraDocumentalVersion();
                    $estructuraDocumentalVersion->setCodigoDirectorioPadre($estructuraDocumental->getCodigoDirectorioPadre());
                    $estructuraDocumentalVersion->setCodigoDirectorio($estructuraDocumental->getCodigoDirectorio());
                    $estructuraDocumentalVersion->setDescripcion($estructuraDocumental->getDescripcion());
                    $estructuraDocumentalVersion->setIdestructura($estructuraDocumental->getIdestructura());
                    $estructuraDocumentalVersion->setEstadoId($estructuraDocumental->getEstadoId());
                    $estructuraDocumentalVersion->setType($estructuraDocumental->getType());
                    $estructuraDocumentalVersion->setVersion($versionActual);
                    $estructuraDocumentalVersion->setFechaVersion($fechaVersion);
                    $this->em->persist($estructuraDocumentalVersion);
                    //Construyo objeto TRD Version
                    $tablaRetencionVersion = new TablaRetencionVersion();
                    $tablaRetencion = $this->em->getRepository(TablaRetencion::class)->findOneBy(array("estructura_documental_id" => $estructuraDocumental->getId()));
                    if (null !== $tablaRetencion) {
                        $tablaRetencionVersion->setEstructuraDocumentalVersion($estructuraDocumentalVersion);
                        $tablaRetencionVersion->setTablaRetencionId($tablaRetencion->getId());
                        $tablaRetencionVersion->setVersion($versionActual);
                        $tablaRetencionVersion->setTiempoRetencionArchivoGestion($tablaRetencion->getTiempoRetencionArchivoGestion());
                        $tablaRetencionVersion->setUnidadRetencionArchivoGestion($tablaRetencion->getUnidadRetencionArchivoGestion());
                        $tablaRetencionVersion->setTiempoRetencionArchivoCentral($tablaRetencion->getTiempoRetencionArchivoCentral());
                        $tablaRetencionVersion->setUnidadRetencionArchivoCentral($tablaRetencion->getUnidadRetencionArchivoCentral());
                        $tablaRetencionVersion->setTipoSoporte($tablaRetencion->getTipoSoporte());
                        $tablaRetencionVersion->setDisposicionFinalBorrar($tablaRetencion->getDisposicionFinalBorrar());
                        $tablaRetencionVersion->setDisposicionFinalConservacionTotal($tablaRetencion->getDisposicionFinalConservacionTotal());
                        $tablaRetencionVersion->setDisposicionFinalConservacionTotal($tablaRetencion->getDisposicionFinalConservacionTotal());
                        $tablaRetencionVersion->setDisposicionFinalConservacionDigital($tablaRetencion->getDisposicionFinalConservacionDigital());
                        $tablaRetencionVersion->setDisposicionFinalMicrofilmado($tablaRetencion->getDisposicionFinalMicrofilmado());
                        $tablaRetencionVersion->setDisposicionFinalSeleccion($tablaRetencion->getDisposicionFinalSeleccion());
                        $tablaRetencionVersion->setDisposicionFinalMigrar($tablaRetencion->getDisposicionFinalMigrar());
                        $tablaRetencionVersion->setDisposicionFinalDigitalizacionMicrofilmacion($tablaRetencion->getDisposicionFinalDigitalizacionMicrofilmacion());
                        $tablaRetencionVersion->setProcedimientoDisposicion($tablaRetencion->getProcedimientoDisposicion());
                        $tablaRetencionVersion->setLeyNormatividad($tablaRetencion->getLeyNormatividad());
                        $tablaRetencionVersion->setModulo($tablaRetencion->getModulo());
                        $tablaRetencionVersion->setInicioVigencia($tablaRetencion->getInicioVigencia());
                        $tablaRetencionVersion->setEstadoId($tablaRetencion->getEstadoId());
                        $tablaRetencionVersion->setTransferenciaMedioElectronico($tablaRetencion->getTransferenciaMedioElectronico());
                        $tablaRetencionVersion->setTransferenciaMedioElectronico($tablaRetencion->getTransferenciaMedioElectronico());
                        $tablaRetencionVersion->setDireccionDocumentosAlmacenadosElectronicamente($tablaRetencion->getDireccionDocumentosAlmacenadosElectronicamente());
                        $tablaRetencionVersion->setFechaVersion($fechaVersion);
                        $this->em->persist($tablaRetencionVersion);
                    }
                }
            }
            //$this->em->flush();
            //$this->em->getRepository(EstructuraDocumental::class)->updateVersion($versionNueva);
            //$this->em->getRepository(TablaRetencion::class)->updateVersion($versionNueva);
            return array("result" => array("response" => "Se ha generado existomsamente la versión " . $versionNueva));
            /*
        Se procede a consultar toda la TRD y se guarda en la entidad EstructuraDocumentalVersion con la versión actual
         */
        } else {
            return array("result" => array("response" => "No hay nada para versionar"));
        }
        /*
     */
    }

    private function findTreeDocumentalEstructure($estructuraDocumental, $hashParents, $listToVersion, $hashSeledtedParents, $requireRetentionTable) {
        if($requireRetentionTable && $estructuraDocumental->getTablaRetencion() == null && $estructuraDocumental->getType() != 'tipo_documental') {
            return "No es posible realizar el versionamiento porque falta la tabla de retención en " 
                . $estructuraDocumental->getCodigoDirectorio() . "-" . $estructuraDocumental->getDescripcion();
        } else if(isset($hashSeledtedParents[$estructuraDocumental->getCodigoDirectorio()])) {
            return "";
        } else {
            if($estructuraDocumental->getType() != 'tipo_documental') {
                $listToVersion[] = $estructuraDocumental;
                $hashSeledtedParents[$estructuraDocumental->getCodigoDirectorio()] = true;
                $requireRetentionTable = false;
            } else if($estructuraDocumental->getTablaRetencion() != null) {
                $listToVersion[] = $estructuraDocumental;
            }

            if($estructuraDocumental->getCodigoDirectorioPadre() == '-1') {
                return "";
            } else {
                $requireRetentionTable = false;
                return $this->findTreeDocumentalEstructure($hashParents[$estructuraDocumental->getCodigoDirectorioPadre()], $hashParents, $listToVersion, $hashSeledtedParents, $requireRetentionTable);
            }
        }
    }
}
