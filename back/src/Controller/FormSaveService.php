<?php

namespace App\Controller;

use App\Entity\Formulario;
use App\Entity\Usuario;
use App\Entity\EstructuraDocumental;
use App\Entity\Grupo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class FormSaveService
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
     * @param string $request
     *
     * @return Usuario
     */
    public function Save(Request $request)
    {
        //consulto el formulario que se quiere duplicar
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($request->attributes->get("id"));
        $data = json_decode($request->getContent());

        $formulario->setTipoFormulario($data->{"tipoFormulario"});
        //$formulario->setVersion($data->{"version"});
        $formulario->setFechaVersion(new \DateTime($data->{"fechaVersion"}));
        $formulario->setNombre($data->{"nombre"});

        if (strlen(trim($data->{"nomenclaturaFormulario"})) > 0) {
            $formulario->setNomenclaturaFormulario(trim($data->{"nomenclaturaFormulario"}));
        } else {
            $formulario->setNomenclaturaFormulario($data->{"nomenclaturaFormulario"});
        }
        
        //$formulario->setFormularioTransversal($data->{"formulario_transversal"});
        //$formulario->setPermiteTareas($data->{"permite_tareas"});
        $formulario->setGeneraPdfConFirmaDigital($data->{"generaPdfConFirmaDigital"});
        //$formulario->setRadicadoElectronico($data->{"radicado_electronico"});
        $formulario->setTipoSticker($data->{"tipoSticker"});
        $formulario->setInicioVigencia(new \DateTime($data->{"inicioVigencia"}));
        if (isset($data->{"finVigencia"})) {
            $formulario->setFinVigencia(new \DateTime($data->{"finVigencia"}));
        } else {
            $formulario->setFinVigencia(null);
        }
        
        $formulario->setAyuda($data->{"ayuda"});
        $formulario->setEstadoId($data->{"estadoId"});
        //$formulario->setTablaRetencionDisposicionFinalConservacionDigital($data->{"tabla_retencion_disposicion_final_conservacion_digital"});
        //$formulario->setFlujoTrabajoId($data->{"flujo_trabajo_id"});
        $estructuraDocumentalId = str_replace("api/estructura_documentals/", "", $data->{"estructuraDocumental"});

        if ($formulario->getEstructuraDocumental() != null && $formulario->getEstructuraDocumentalId() != $estructuraDocumentalId) {
            $estructuraDocumentalOld = $this->em->getRepository(EstructuraDocumental::class)->findOneById($formulario->getEstructuraDocumentalId());
            $estructuraDocumentalOld->setFormulario(null);
            $this->em->persist($estructuraDocumentalOld);
        }

        $formulario->setEstructuraDocumentalId($estructuraDocumentalId);
        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findOneById($estructuraDocumentalId);

        //$formulario->setFlujoTrabajo($data->{"flujoTrabajo"});
        $formulario->setEstructuraDocumental($estructuraDocumental);
        $estructuraDocumental->setFormulario($formulario);

        
        /*
        $formulario->clearGrupo();

        $grupos = $data->{'grupos'};
        foreach($grupos as $grupoId) {
            $grupo = $this->em->getRepository(Grupo::class)->findOneById(str_replace("/api/grupos/", "", $grupoId));
            $formulario->addGrupo($grupo);
        }
        */

        $this->em->persist($formulario);
        $this->em->persist($estructuraDocumental);
        $this->em->flush();
        if (isset($formulario)) {
            return $formulario;
        } else {
            return array("response" => "El formulario no se pudo actualizar");
        }
    }
}
