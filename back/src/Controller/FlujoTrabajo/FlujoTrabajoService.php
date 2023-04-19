<?php

namespace App\Controller\FlujoTrabajo;

use App\Entity\FlujoTrabajo;
use App\Entity\Formulario;
use App\Entity\FormularioVersion;
use App\Entity\FlujoTrabajoVersion;
use App\Entity\Paso;
use App\Entity\PasoVersion;
use App\Entity\PasoEvento;
use App\Entity\PasoEventoVersion;
use App\Entity\PlantillaVersion;
use App\Exceptions\WorkFlowException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Undocumented class
 */
class FlujoTrabajoService
{

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

    public function create($request)
    {
        $data = json_decode($request->getContent());
        
        $nombre = $data->{"nombre"};
        $descripcion = $data->{"descripcion"};
        $estado = $data->{"estadoId"};
        $version = $data->{"version"};
        $formulario = $data->{"formularioId"};
        
        if (isset($nombre) ||
            isset($descripcion) ||
            isset($estado) ||
            isset($version) ||
            isset($formulario)) {
            $flujoTrabajo= new FlujoTrabajo();
            $flujoTrabajo->setNombre($nombre);
            $flujoTrabajo->setDescripcion($descripcion);
            $flujoTrabajo->setEstadoId($estado);
            $flujoTrabajo->setVersion($version);
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($formulario);
            $flujoTrabajo->setFormulario($formulario);
            $this->em->persist($flujoTrabajo);
            $this->em->flush();
            
            return ($flujoTrabajo);
        }else{
            return array("response" => "Campos incompletos");
        }        
    }

    public function version($request)
    {
        $workFlow = $this->em->getRepository(FlujoTrabajo::class)->findOneById($request->attributes->get('id'));
        $this->validateWorkFlow($workFlow);

        $pasos = $this->em->getRepository(Paso::class)->findBy(array("flujo_trabajo_id" => $workFlow->getId(), "estado_id" => 1), array("numero" => "ASC"));
        $this->validateSteps($pasos, $workFlow->getNombre());

        $version = $workFlow->getVersion();
        $version++;
        $workFlow->setVersion($version);

        $formulario = $this->em->getRepository(Formulario::class)->findOneById($workFlow->getFormularioId());
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneBy(array("formulario_id" => $formulario->getId(), "version" => $formulario->getVersion()));

        if(!isset($formularioVersion)) {
            throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "El formulario asociado al flujo de trabajo no ha sido versionado");
        }

        $workFlowVersion = new FlujoTrabajoVersion();
        $workFlowVersion->setEstadoId(1);
        $workFlowVersion->setNombre($workFlow->getNombre());
        $workFlowVersion->setDescripcion($workFlow->getDescripcion());
        $workFlowVersion->setVersion($workFlow->getVersion());
        $workFlowVersion->setFlujoTrabajo($workFlow);
        $workFlowVersion->setFormularioVersion($formularioVersion);

        $this->em->persist($workFlowVersion);
        $this->em->flush();

        foreach($pasos as $paso) {
            $this->validateEvents($paso);
            $workFlowVersion->addPasoVersion($this->generatePasoVersion($paso, $workFlowVersion));
        }

        $this->em->persist($workFlowVersion);
        $this->em->persist($workFlow);
        $this->em->flush();
        return array("response" => $workFlow);
    }

    private function generatePasoVersion(Paso $paso, FlujoTrabajoVersion $flujoTrabajoVersion) {
        $pasoVersion = new PasoVersion();
        $pasoVersion->setPrioridad($paso->getPrioridad());
        $pasoVersion->setDescripcion($paso->getDescripcion());
        $pasoVersion->setFlujoTrabajoVersion($flujoTrabajoVersion);
        $pasoVersion->setEstadoId(1);
        $pasoVersion->setPlazo($paso->getPlazo());
        $pasoVersion->setTime($paso->getTime());
        $pasoVersion->setNumero($paso->getNumero());
        $pasoVersion->setPasoId($paso->getId());
        $this->em->persist($pasoVersion);
        $this->em->flush();

        foreach($paso->getPasoEventos() as $pasoEvento) {
            $pasoVersion->addPasoEventoVersion($this->generateEventoVersion($pasoEvento, $pasoVersion));
        }

        $this->em->persist($pasoVersion);
        $this->em->flush();

        return $pasoVersion;
    }

    private function generateEventoVersion(PasoEvento $pasoEvento, PasoVersion $pasoVersion) {
        $pasoEventoVersion = new PasoEventoVersion();
        $pasoEventoVersion->setPasoVersion($pasoVersion);
        $pasoEventoVersion->setEvento($pasoEvento->getEvento());
        $pasoEventoVersion->setPasoEventoId($pasoEvento->getId());
        $pasoEventoVersion->setFatherId($pasoEvento->getFatherId());
        $pasoEventoVersion->setConfig($pasoEvento->getConfig());

        if ($pasoEvento->getFatherId() == 2) {
            $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findBy(array("formulario_id" => $pasoEvento->getConfig()["formulario_id"]), array("version" => "DESC"))[0];
            $config = array();
            $config["nombre"] = $pasoEvento->getConfig()["nombre"];
            $config["formulario_id"] = $formularioVersion->getId();
            $pasoEventoVersion->setConfig($config);
        } elseif ($pasoEvento->getFatherId() == 27) {
            $flujosTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->findBy(array("flujo_trabajo_id" => $pasoEvento->getConfig()["flujo_id"]), array("version" => "DESC"));
            $flujoTrabajoVersion = $flujosTrabajoVersion[0];
            $config = array();
            $config["flujo_id"] = $flujoTrabajoVersion->getId();
            $config["flujo_name"] = $pasoEvento->getConfig()["flujo_name"];
            $config["active_auto"] = $pasoEvento->getConfig()["active_auto"];
            $pasoEventoVersion->setConfig($config);
        } elseif ($pasoEvento->getFatherId() == 24) {
            $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findBy(array("formulario_id" => $pasoEvento->getConfig()["formulario_id"]), array("version" => "DESC"))[0];
            $plantillas = $this->em->getRepository(PlantillaVersion::class)->findBy(array("plantilla_id" => $pasoEvento->getConfig()["plantilla_id"], "formulario_version_id" => $formularioVersion->getId()));
            $plantilla = $plantillas[0];
            $config = array();
            $config["plantilla_id"] = $plantilla->getId();
            $config["descripcion"] = $plantilla->getDescripcion();
            $config["formulario_id"] = $plantilla->getFormularioVersionId();
            $pasoEventoVersion->setConfig($config);
        }

        $this->em->persist($pasoEventoVersion);
        $this->em->flush();

        return $pasoEventoVersion;
    }

    private function validateWorkflow(FlujoTrabajo $workFlow) {
        $formulario = $workFlow->getFormulario();
        
        if ($formulario == null) {
            throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "No se ha asociado un formulario al flujo de trabajo " . $workFlow->getNombre());
        } elseif($formulario->getVersion() == 0){
            throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "El formulario asociado al flujo de trabajo no ha sido versionado");
        }
    }

    private function validateSteps($pasos, $workFlowName) {

        if (count($pasos) == 0) {
            throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "El flujo de trabajo " . $workFlowName . " no tiene pasos, debe crearselos");
        }

        // validar orden de pasos
        $numeroPaso = 0;
        foreach($pasos as $paso) {
            $numeroPaso++;
            if($numeroPaso != $paso->getNumero()) {
                throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "En el flujo de trabajo " . $workFlowName . " no se encontró el paso número: " . $numeroPaso);
            }

            // validar eventos del paso
            $this->validateEvents($paso);
        }
    }

    private function validateEvents($paso) {
        $events = $paso->getPasoEventos();
        $hasResponsible = false;
        foreach($events as $event) {
            switch($event->getFatherId()) {
                case 1: //Evento responsable
                    $hasResponsible = true;
                    break;
                case 27: //Iniciar flujo
                    $flujosTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->findBy(array("flujo_trabajo_id" => $event->getConfig()["flujo_id"]), array("version" => "DESC"));
                    if(count($flujosTrabajoVersion) == 0) {
                        throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "El flujo de trabajo " . trim($event->getConfig()["flujo_name"]) . ", se debe versionar para iniciar un flujo desde el paso: " . $paso->getDescripcion());
                    }
                    break;
            }
        }

        if (!$hasResponsible) {
            throw new WorkFlowException(Response::HTTP_PRECONDITION_FAILED, "No se ha configurado el evento responsable para el paso " . $paso->getDescripcion());
        }
    }
}
