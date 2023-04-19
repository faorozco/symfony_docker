<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use App\Entity\Formulario;
use App\Entity\Valordocumental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Undocumented class
 */
class TablaRetencionSaveService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $entityManager;
        $this->encoder = $encoder;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function save(Request $request)
    {
        $data = json_decode($request->getContent());
        $tablaRetencion = $this->em->getRepository(TablaRetencion::class)->findOneById($request->attributes->get("id"));
        $tablaRetencion->setTiempoRetencionArchivoGestion($data->{"tiempoRetencionArchivoGestion"});
        $tablaRetencion->setUnidadRetencionArchivoGestion($data->{"unidadRetencionArchivoGestion"});
        $tablaRetencion->setTiempoRetencionArchivoCentral($data->{"tiempoRetencionArchivoCentral"});
        $tablaRetencion->setUnidadRetencionArchivoCentral($data->{"unidadRetencionArchivoCentral"});
        $tablaRetencion->setTipoSoporte($data->{"tipoSoporte"});
        $tablaRetencion->setDisposicionFinalBorrar($data->{"disposicionFinalBorrar"});
        $tablaRetencion->setDisposicionFinalConservacionTotal($data->{"disposicionFinalConservacionTotal"});
        $tablaRetencion->setDisposicionFinalConservacionDigital($data->{"disposicionFinalConservacionDigital"});
        $tablaRetencion->setDisposicionFinalMicrofilmado($data->{"disposicionFinalMicrofilmado"});
        $tablaRetencion->setDisposicionFinalSeleccion($data->{"disposicionFinalSeleccion"});
        $tablaRetencion->setProcedimientoDisposicion($data->{"procedimientoDisposicion"});
        $tablaRetencion->setInicioVigencia(new \DateTime($data->{"inicioVigencia"}));
        $tablaRetencion->setLeyNormatividad($data->{"leyNormatividad"});
        $tablaRetencion->setDisposicionFinalDigitalizacionMicrofilmacion($data->{"disposicionFinalDigitalizacionMicrofilmacion"});
        $tablaRetencion->setDisposicionFinalMigrar($data->{"disposicionFinalMigrar"});
        $tablaRetencion->setTransferenciaMedioElectronico($data->{"transferenciaMedioElectronico"});
        $tablaRetencion->setDireccionDocumentosAlmacenadosElectronicamente($data->{"direccionDocumentosAlmacenadosElectronicamente"});
        $tablaRetencion->setEstadoId($data->{"estadoId"});

        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findOneById($data->{"estructuraDocumentalId"});

        //quitar relacion de la estructura documental con otro formulario diferente al que se esta asociando
        $formulariosRelated = $this->em->getRepository(Formulario::class)->findBy(array("estructura_documental_id" => $estructuraDocumental->getId()));
        foreach ($formulariosRelated as $formularioRelated) {
            if (isset($data->{"formularioId"}) && $formularioRelated->getId() != $data->{"formularioId"}) {
                $formularioRelated->setEstructuraDocumental(null);
                $this->em->persist($formularioRelated);
                $this->em->flush();
            }
        }

        $oldEstructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findOneById($data->{"oldEstructuradocumentalId"});
        if ($oldEstructuraDocumental != null && $data->{"oldEstructuradocumentalId"} != $data->{"estructuraDocumentalId"}) {
            
            $oldEstructuraDocumental->setTablaRetencion(null);
            if ($oldEstructuraDocumental->getFormularioId() != null) {
                $oldFormulario = $this->em->getRepository(Formulario::class)->findOneById($oldEstructuraDocumental->getFormularioId());
                $oldFormulario->setEstructuraDocumental(null);
                $this->em->persist($oldFormulario);            
            }
            $oldEstructuraDocumental->setFormularioId(null);
            $this->em->persist($oldEstructuraDocumental);
        }

        
        if (isset($data->{"formularioId"})) {
            if ($data->{"oldEstructuradocumentalId"} == $data->{"estructuraDocumentalId"}) {
                $oldFormularioId = $estructuraDocumental->getFormularioId();
                if ($oldFormularioId && $oldFormularioId != $data->{"formularioId"}) {
                    $oldFormulario = $this->em->getRepository(Formulario::class)->findOneById($oldEstructuraDocumental->getFormularioId());
                    $oldFormulario->setEstructuraDocumental(null);
                    $this->em->persist($oldFormulario);
                }
            }

            $formulario = $this->em->getRepository(Formulario::class)->findOneById($data->{"formularioId"});
            $estructuraDocumental->setFormulario($formulario);
            $formulario->setEstructuraDocumental($estructuraDocumental);
        } /*else {
            $formularioId = $estructuraDocumental->getFormularioId();
            if ($formularioId != null) {
                $formulario = $this->em->getRepository(Formulario::class)->findOneById($formularioId);
                $formulario->setEstructuraDocumental(null);
                $this->em->persist($formulario);
            }
            $estructuraDocumental->setFormularioId(null);
        } */
        
        $tablaRetencion->setEstructuraDocumentalId($data->{"estructuraDocumentalId"});
        $tablaRetencion->setEstructuraDocumental($estructuraDocumental);
        $estructuraDocumental->setTablaRetencion($tablaRetencion);

        if (isset($data->{"valordocumentals"})) {
            //se borran los objetos de la relación actual
            foreach ($tablaRetencion->getValorDocumentals() as $valorDocumental) {
                $tablaRetencion->removeValorDocumental($valorDocumental);
            }

            //se agregan los nuevos objetos
            foreach ($data->{"valordocumentals"} as $valorDocumentalId) {

                $valorDocumental = $this->em->getRepository(Valordocumental::class)->findOneById($valorDocumentalId);
                if (null !== $valorDocumental) {
                    $tablaRetencion->addValorDocumental($valorDocumental);
                } else {
                    throw new \Exception('Valor Documental no existe.');
                }
            }
        }

        if (isset($formulario)) {
            $this->em->persist($formulario);
        }
        
        $this->em->persist($tablaRetencion);
        $this->em->persist($estructuraDocumental);
        $this->em->flush();
        return $tablaRetencion;
    }
}
