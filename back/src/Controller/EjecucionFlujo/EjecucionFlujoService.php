<?php

namespace App\Controller\EjecucionFlujo;

use App\Controller\EjecucionPaso\EjecucionPasoService as EjecucionPasoEjecucionPasoService;
use App\Entity\EjecucionFlujo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Registro;
use App\Entity\EjecucionPaso;
use App\Entity\FlujoTrabajoVersion;
use App\Entity\PasoVersion;
use App\Entity\PasoEventoVersion;
use App\Entity\Usuario;
use App\Exceptions\ExecutionException;
use App\Utils\Constant\WorkFlowConstant;
use App\Utils\Constant\ResponseCode;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\EjecucionPaso\EjecucionPasoService;
use App\Controller\RegistroFormularioVersionService;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Controller\Notificacion\NotificacionService;

/**
 * Undocumented class
 */
class EjecucionFlujoService
{
    private $_em;
    private $ejecucionPasoService;
    private RegistroFormularioVersionService $registroFormularioVersionService;
    private NotificacionService $notificacionService;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, KernelInterface $kernel)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->ejecucionPasoService = new EjecucionPasoService($entityManager, $tokenStorage, $kernel);
        $this->registroFormularioVersionService = new RegistroFormularioVersionService($entityManager, $tokenStorage);
        $this->notificacionService = new NotificacionService($entityManager, $tokenStorage, $kernel);
    }

    public function iniciar($request)
    {     
        $flujoVersionId = $request->attributes->get("id");
        $registroId = $request->query->get("registroId");
        $usuarioId = $request->query->get("usuarioId");
        $ejecucionPasoId = $request->query->get("ejecucionPasoId");
        $crearRegistro = $request->query->get("crearRegistro");

        if ($crearRegistro != null && $crearRegistro == "true") {
            $registroId = $this->crearRegistroInicioFlujo($flujoVersionId);
        }

        if (isset($flujoVersionId) && isset($registroId)) {
            $usuario = $this->tokenStorage->getToken()->getUser();
            $registro = $this->em->getRepository(Registro::class)->findOneById($registroId);
            $flujoTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->findOneById($flujoVersionId);
            $pasoVersion = $this->ejecucionPasoService->primerPaso($flujoTrabajoVersion);

            if ($pasoVersion != null) {
                $ejecucionFlujo = new EjecucionFlujo();
                
                $ejecucionFlujo->setFechaInicio(new \DateTime(date("Y-m-d H:i")));
                $ejecucionFlujo->setFlujoTrabajoVersion($flujoTrabajoVersion);
                if($registro != null) $ejecucionFlujo->setRadicado($registro->getRadicado());
                $ejecucionFlujo->setUsuario($usuario);
                $ejecucionFlujo->setEstado(WorkFlowConstant::FLOW_ACTIVE);

                $ejecucionPaso = new EjecucionPaso();
                $ejecucionPaso->setEstado(WorkFlowConstant::STEP_ACTIVE);
                $ejecucionPaso->setPasoVersion($pasoVersion);
                $ejecucionPaso->setFechaInicio($ejecucionFlujo->getFechaInicio());
                $ejecucionPaso->setFechaVencimiento($this->ejecucionPasoService->calcularFechaVencimiento($ejecucionPaso->getFechaInicio(), $pasoVersion->getPlazo()));
                $ejecucionPaso->setUsuarioRemitente($usuario);
                $this->ejecucionPasoService->asignarResponsable($ejecucionPaso, $pasoVersion, $registro, $usuarioId);

                $this->em->persist($ejecucionFlujo);
                $this->em->flush();
                $ejecucionPaso->setEjecucionFlujo($ejecucionFlujo);

                $this->em->persist($ejecucionPaso);

                if($ejecucionPasoId != null) {
                    $ejecucionPasoIniciarFlujo = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
                    $ejecucionPasoIniciarFlujo->setEjecucionFlujoIniciado($ejecucionFlujo);
                }

                $this->em->flush();

                //TODO notificar responsable de este paso
                $this->ejecucionPasoService->notificar($ejecucionPaso, $pasoVersion, $registro, 6);

                return array("response" => "El flujo fue iniciado correctamente", "ejecucionFlujoId" => $ejecucionFlujo->getId());
            } else {
                return array("response" => "No existen pasos asociados");
            }
        }else{
            return array("response" => "Se presento un error al iniciar el flujo");
        }
            
        
        
    }

    public function consultarRadicado($radicado, $filter, $order, $page, $size)
    {     
        $usuario = $this->tokenStorage->getToken()->getUser();
        if (isset($radicado)) {
            $flujos = $this->em->getRepository(EjecucionPaso::class)->buscarFlujoRadicado($radicado, $filter, $order, $usuario->getId(), $page, $size);
            return $flujos;
        }else{
            return array("response" => "Se presento un error al consultar flujos por radicado");
        }
    }

    public static function completar($em, $id) {
        $ejecucionFlujo = $em->getRepository(EjecucionFlujo::class)->findOneById($id);

        $ejecucionFlujo->setFechaFin(new \DateTime(date("Y-m-d H:i")));
        $ejecucionFlujo->setEstado(WorkFlowConstant::FLOW_COMPLETED);
        $em->persist($ejecucionFlujo);
        $em->flush();
    }

    public static function interrumpir($em, $id) {
        $ejecucionFlujo = $em->getRepository(EjecucionFlujo::class)->findOneById($id);

        $ejecucionFlujo->setFechaFin(new \DateTime(date("Y-m-d H:i")));
        $ejecucionFlujo->setEstado(WorkFlowConstant::FLOW_DISRUPTED);
        $em->persist($ejecucionFlujo);
        $em->flush();
    }

    public function crearRegistroInicioFlujo($flujoVersionId) {
        $flujoTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->findOneById($flujoVersionId);
        $formularioVersionId = $flujoTrabajoVersion->getFormularioVersionId();
        $estadoId = 1;
        $ejecucionFlujoId = null;
        $tipoCorrespondencia = null;
        $ejecucionPasoId = null;
        $registros = [];
        $registro = $this->registroFormularioVersionService->save(
            $formularioVersionId,
            $estadoId,
            $ejecucionFlujoId,
            $tipoCorrespondencia,
            $ejecucionPasoId,
            $registros,
            null,
            true
        );

        return $registro->getId();
    }

    public function consultarPorUsuario($filter, $order, $page, $size)
    {     
        $usuario = $this->tokenStorage->getToken()->getUser();
        if (isset($usuario)) {
            $flujos = $this->em->getRepository(EjecucionPaso::class)->buscarFlujoPorUsuario($usuario->getId(), $filter, $order, $page, $size);
            return $flujos;
        }else{
            return array("response" => "Se presento un error al consultar flujos por radicado");
        }
    }

    public function consultarPorId($ejecucionFlujoId)
    {     
        if (isset($ejecucionFlujoId)) {
            $flujos = $this->em->getRepository(EjecucionPaso::class)->buscarFlujoPorId($ejecucionFlujoId);
            return $flujos;
        }else{
            return array("response" => "Se presento un error al consultar el flujo por id");
        }
    }
}
