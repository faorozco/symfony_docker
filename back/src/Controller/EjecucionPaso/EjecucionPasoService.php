<?php

namespace App\Controller\EjecucionPaso;

use App\Entity\EjecucionFlujo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Registro;
use App\Entity\EjecucionPaso;
use App\Entity\FlujoTrabajoVersion;
use App\Entity\Paso;
use App\Entity\PasoVersion;
use App\Entity\PasoEventoVersion;
use App\Entity\Usuario;
use App\Exceptions\ExecutionException;
use App\Utils\Constant\WorkFlowConstant;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\EjecucionFlujo\EjecucionFlujoService;
use App\Controller\PasoVersion\PasoVersionService;
use App\Controller\Registro\RegistroService;
use App\Controller\Comments\SaveCommentsService;
use App\Controller\Notificacion\NotificacionService;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Undocumented class
 */
class EjecucionPasoService
{
    private $_em;
    private $pasoVersionService;
    private $registroService;
    private $saveComments;
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
        $this->pasoVersionService = new PasoVersionService($entityManager, $tokenStorage);
        $this->registroService = new RegistroService($entityManager);
        $this->saveComments = new SaveCommentsService($entityManager);
        $this->notificacionService = new NotificacionService($entityManager, $tokenStorage, $kernel);
    }

    public function completar($request)
    {     
        $ejecucionPasoId = $request->attributes->get("id");
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);

        $radicado = $request->query->get("radicado");
        $interrumpir = $request->query->get("interrumpir");
        $responsableSiguientePasoId = $request->query->get("responsableSiguientePaso");
        $usuarioResponsableVistoBuenoId = $request->query->get("usuarioResponsableVistoBuenoId");
        $registro = $this->em->getRepository(Registro::class)->findBy(array("radicado" => $radicado))[0];

        if($ejecucionPaso == null) {
            throw new ExecutionException(Response::HTTP_BAD_REQUEST, "El id de la ejecución del paso es inválido");
        }
        if ($usuarioResponsableVistoBuenoId != null && $ejecucionPaso->getEstado() == WorkFlowConstant::STEP_ACTIVE) {
            $properties = array(
                "radicado" => $radicado,
                "interrumpir" => $interrumpir,
                "responsableSiguientePasoId" => $responsableSiguientePasoId,
                "usuarioResponsableVistoBuenoId" => $usuarioResponsableVistoBuenoId
            );

            $ejecucionPaso->setTempProperties($properties);
            $this->requerirVistoBueno($ejecucionPaso);
            return array("response" => "El paso fue asignado correctamente al responsable de dar el visto bueno");
        } else if ($ejecucionPaso->getEstado() == WorkFlowConstant::STEP_APPROVAL) {
            $properties = $ejecucionPaso->getTempProperties();
            $radicado = $properties["radicado"];
            $interrumpir = $properties["interrumpir"];
            $responsableSiguientePasoId = $properties["responsableSiguientePasoId"];
            $usuarioResponsableVistoBuenoId = $properties["usuarioResponsableVistoBuenoId"];
        }
        
        return $this->completarPasoActual($ejecucionPaso, $interrumpir, $registro, $responsableSiguientePasoId);
    }

    public function completarPasoActual($ejecucionPaso, $interrumpir, $registro, $responsableSiguientePasoId) {

        if($interrumpir=="true"){
            $this->interrumpirPaso($ejecucionPaso);
            EjecucionFlujoService::interrumpir($this->em, $ejecucionPaso->getEjecucionFlujo()->getId());
            return array("response" => "Se interrumpio el flujo y se finaliza el flujo correctamente");
        }

        $this->completarPaso($ejecucionPaso, $registro);
        if($this->finAnticipado($ejecucionPaso)) {
            EjecucionFlujoService::completar($this->em, $ejecucionPaso->getEjecucionFlujo()->getId());
            return array("response" => "Se completó el paso y se finaliza el flujo anticipadamente correctamente");
        }

        $siguienteEjecucionPaso = null;
        if (!$this->finalizarFlujo($ejecucionPaso)) {
            $siguienteEjecucionPaso = $this->siguiente($ejecucionPaso, $registro, $responsableSiguientePasoId);
        }
        
        
        if($siguienteEjecucionPaso != null) {
            $ejecucionPaso->setEjecucionPasoIdSiguiente($siguienteEjecucionPaso->getId());
            $this->em->persist($ejecucionPaso);
            $this->em->flush(); 

            // notificar responsable
            $pasoVersion = $this->em->getRepository(PasoVersion::class)->findOneById($siguienteEjecucionPaso->getPasoVersion()->getId());
            $this->notificar($siguienteEjecucionPaso, $pasoVersion, $registro, 6);

            return array("response" => "Se completó el paso correctamente");
        } else {
            EjecucionFlujoService::completar($this->em, $ejecucionPaso->getEjecucionFlujo()->getId());
            return array("response" => "Se completó el paso y se finaliza el flujo correctamente");
        }      
    }

    public function primerPaso(FlujoTrabajoVersion $flujoTrabajoVersion) {
        $pasoVersion = $this->em->getRepository(PasoVersion::class)->findBy(array("flujo_trabajo_version_id" => $flujoTrabajoVersion->getId(), "numero" => 1));

        if (isset($pasoVersion) && count($pasoVersion) == 1) {
            return $pasoVersion[0];
        } else return null;
    }

    public function asignarResponsable(EjecucionPaso $ejecucionPaso, PasoVersion $pasoVersion, $registro, $usuarioId) {
        $eventos = $this->em->getRepository(PasoEventoVersion::class)->findBy(array("paso_version_id" => $pasoVersion->getId(), "fatherId" => 1));

        if (count($eventos) == 0) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "No hay un evento responsable configurados para el paso " . $pasoVersion->getDescripcion());
        }

        $pasoEventoVersion = $eventos[0];
        $userId = null;
        $config = $pasoEventoVersion->getConfig();
        switch ($pasoEventoVersion->getEventoId()) {
            case 1: //Seleccion de responsable unico para el paso
                if (isset($config["user_id"])) {
                    $userId = $config["user_id"];
                }
                break;
            case 2: //Seleccion del responsable mediante una condicion desde los campos del formulario de inicio
                $userId = $this->asignarResponsablePorCondicion($ejecucionPaso->getEjecucionFlujo()->getId(), $config);
                break;
            case 3: //Asignacion de responsable por carga de trabajo desde un grupo de usuarios
                $userId = $this->em->getRepository(EjecucionPaso::class)->findUserResponsibleWorkload($this->em, $config["grupo_id"]);
                break;
            case 4: //Seleccionar responsable desde los  usuarios del sistema
                $userId = $usuarioId;
                break;
            case 5: //Responsable por asignacion manual desde un grupo
                $userId = $usuarioId;
                break;
            case 6: //El responsable del paso es la persona que radico el documento
                $userId = $registro->getUsuarioId();
                break;
            case 7: //Selecionar responsable desde la lista de usuarios del sistema
                $userId = $usuarioId;
                break;
            case 8: //Responsable por asignacion consecutiva de un grupo
                $users = $this->em->getRepository(Usuario::class)->findUsersResponsibleByGroup($this->em, $config["grupo_id"]);
                $ultimoEjecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findBy(array("usuario_responsable_id" => $users), array("id" => "DESC"))[0];
                
                $userId = $users[0];
                $selectNext = false;
                foreach($users as $user) {
                    if($selectNext) {
                        $userId = $user;
                        break;
                    } else if($user == $ultimoEjecucionPaso->getUsuarioResponsableId()) {
                        $selectNext = true;
                    }
                }

                break;
            case 9: //Responsable desde grupo formulario de inicio
                $userId = $usuarioId;
                break;
            case 36: //Asignar responsable desde variable del formulario de inicio
                $value = $this->registroService->valueByFieldAndRegister($config["variableId"], $registro);
                if ($value == null) {
                    break;
                }
                $users = $this->em->getRepository(Usuario::class)->findUsersResponsibleByFullName($this->em, $value['valor']);
                
                $userId = $users[0];

                break;
        }

        if ($userId != null) {
            $usuario = $this->em->getRepository(Usuario::class)->findOneById($userId);
            $ejecucionPaso->setUsuarioResponsable($usuario);
        } else {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "El paso " . $pasoVersion->getDescripcion() . " no tiene responsable asignado");
        }
        
    }

    public function notificar(EjecucionPaso $ejecucionPaso, PasoVersion $pasoVersion, $registro, $fatherId) {
        $eventos = $this->em->getRepository(PasoEventoVersion::class)->findBy(array("paso_version_id" => $pasoVersion->getId(), "fatherId" => $fatherId));

        if (count($eventos) > 0) {
            $pasoEventoVersion = $eventos[0];
            $config = $pasoEventoVersion->getConfig();

            if($config["active"]){
                switch ($fatherId) {
                    case 6: //Por Email al responsable de este paso
                        $this->notificacionService->responsablePaso($ejecucionPaso->getId(), $registro);
                        break;
                    case 7: //Por Email al responsable de este paso
                        $this->notificacionService->remitentePaso($ejecucionPaso->getId(), $registro);
                        break;
                    case 8: //Por e-mail al remitente cuando se cumple un visto bueno
                        $this->notificacionService->remitenteVistoBueno($ejecucionPaso->getId(), $registro);
                        break;
                }
            }
        }
    }

    public function siguientePasoVersion($ejecucionPasoId) {
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)
                    ->findOneById($ejecucionPasoId);

        $pasoVersion = $ejecucionPaso->getPasoVersion();

        $siguientePasoVersion = $this->siguientePasoPorCondicion($ejecucionPaso);

        if($siguientePasoVersion == null) {
            $result = $this->em->getRepository(PasoVersion::class)
                    ->findBy(array("flujo_trabajo_version_id" => $pasoVersion->getFlujoTrabajoVersionId(), "numero" => $pasoVersion->getNumero() + 1));

            if(count($result) == 0) {
                return array("error" => "No fue encontrado el paso siguiente");
            }

            $siguientePasoVersion = $result[0];
        }

        $acciones = $this->pasoVersionService->cargarAcciones($siguientePasoVersion->getId());
        return array("response"=> array("siguientePasoVersionId" => $siguientePasoVersion->getId(), "acciones" => $acciones["acciones"]));
    }

    private function completarPaso(EjecucionPaso $ejecucionPaso, $registro) {
        $ejecucionPaso->setFechaFin(new \DateTime(date("Y-m-d H:i")));
        $ejecucionPaso->setEstado(WorkFlowConstant::STEP_COMPLETED);
        
        if ($ejecucionPaso->getUsuarioResponsable() == null) {
            $ejecucionPaso->setUsuarioResponsable($this->tokenStorage->getToken()->getUser());
        }

        $this->em->persist($ejecucionPaso);
        $this->em->flush();

        // notificar responsable
        $pasoVersion = $this->em->getRepository(PasoVersion::class)->findOneById($ejecucionPaso->getPasoVersion()->getId());
        $this->notificar($ejecucionPaso, $pasoVersion, $registro, 7);
    }

    private function interrumpirPaso(EjecucionPaso $ejecucionPaso) {
        $ejecucionPaso->setFechaFin(new \DateTime(date("Y-m-d H:i")));
        $ejecucionPaso->setEstado(WorkFlowConstant::STEP_DISRUPTED);
        
        if ($ejecucionPaso->getUsuarioResponsable() == null) {
            $ejecucionPaso->setUsuarioResponsable($this->tokenStorage->getToken()->getUser());
        }

        $this->em->persist($ejecucionPaso);
        $this->em->flush();
    }

    private function requerirVistoBueno(EjecucionPaso $ejecucionPaso) {
        $ejecucionPaso->setFechaFin(new \DateTime(date("Y-m-d H:i")));
        $ejecucionPaso->setEstado(WorkFlowConstant::STEP_APPROVAL);
        
        if ($ejecucionPaso->getUsuarioResponsable() == null) {
            $ejecucionPaso->setUsuarioResponsable($this->tokenStorage->getToken()->getUser());
        }

        $this->em->persist($ejecucionPaso);
        $this->em->flush();
    }

    private function siguiente(EjecucionPaso $ejecucionPaso, $registro, $responsableSiguientePasoId) {
        $pasoVersion = $ejecucionPaso->getPasoVersion();

        if($ejecucionPaso->getDevolucion() == 'Reiniciar'){
            //obtner siguiente ejecucion paso desde la columna
            $idEjecucionPaso = $ejecucionPaso->getEjecucionPasoIdSiguiente();
            $siguienteEjecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($idEjecucionPaso);
            $ejecucionPaso->setDevolucion(null);
            $siguienteEjecucionPaso->setEstado(WorkFlowConstant::STEP_ACTIVE);
            if($siguienteEjecucionPaso->getEjecucionPasoIdSiguiente() != null){
                $siguienteEjecucionPaso->setDevolucion('Reiniciar');
            }
            $siguienteEjecucionPaso->setFechaInicio(new \DateTime(date("Y-m-d H:i")));
            $siguienteEjecucionPaso->setFechaVencimiento($this->calcularFechaVencimiento($siguienteEjecucionPaso->getFechaInicio(), $siguienteEjecucionPaso->getPasoVersion()->getPlazo()));
            $siguienteEjecucionPaso->setFechaFin(null);
            $this->em->persist($siguienteEjecucionPaso);
            $this->em->persist($ejecucionPaso);
            $this->em->flush();
            return $siguienteEjecucionPaso;
        }else if($ejecucionPaso->getDevolucion() == 'Continuar'){
            //obtner siguiente ejecucion paso desde la columna
            $siguienteEjecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findBy(array("ejecucion_flujo_id" => $ejecucionPaso->getEjecucionFlujoId(), "ejecucion_paso_id_siguiente" => null))[0];
            $ejecucionPaso->setDevolucion(null);
            $siguienteEjecucionPaso->setEstado(WorkFlowConstant::STEP_ACTIVE);
            $siguienteEjecucionPaso->setFechaInicio(new \DateTime(date("Y-m-d H:i")));
            $siguienteEjecucionPaso->setFechaVencimiento($this->calcularFechaVencimiento($siguienteEjecucionPaso->getFechaInicio(), $siguienteEjecucionPaso->getPasoVersion()->getPlazo()));
            $siguienteEjecucionPaso->setFechaFin(null);
            $this->em->persist($siguienteEjecucionPaso);
            $this->em->persist($ejecucionPaso);
            $this->em->flush();
            return $siguienteEjecucionPaso;
        }

        $siguientePasoVersion = $this->siguientePasoPorCondicion($ejecucionPaso);

        if ($siguientePasoVersion == null) {
            $result = $this->em->getRepository(PasoVersion::class)
                    ->findBy(array("flujo_trabajo_version_id" => $pasoVersion->getFlujoTrabajoVersionId(), "numero" => $pasoVersion->getNumero() + 1));

            if(count($result) == 0) {
                return null;
            }

            $siguientePasoVersion = $result[0];
        }

        $siguienteEjecucionPaso = new EjecucionPaso();
        $siguienteEjecucionPaso->setEstado(WorkFlowConstant::STEP_ACTIVE);
        $siguienteEjecucionPaso->setPasoVersion($siguientePasoVersion);
        $siguienteEjecucionPaso->setFechaInicio(new \DateTime(date("Y-m-d H:i")));
        $siguienteEjecucionPaso->setFechaVencimiento($this->calcularFechaVencimiento($siguienteEjecucionPaso->getFechaInicio(), $siguientePasoVersion->getPlazo()));
        $siguienteEjecucionPaso->setEjecucionFlujo($ejecucionPaso->getEjecucionFlujo());
        $siguienteEjecucionPaso->setUsuarioRemitente($ejecucionPaso->getUsuarioResponsable());
        $this->asignarResponsable($siguienteEjecucionPaso, $siguientePasoVersion, $registro, $responsableSiguientePasoId);

        $this->em->persist($siguienteEjecucionPaso);
        $this->em->flush();

        return $siguienteEjecucionPaso;
    }

    private function finalizarFlujo($ejecucionPaso) {
        $eventos = $this->em->getRepository(PasoEventoVersion::class)->findBy(array("paso_version_id" => $ejecucionPaso->getPasoVersionId(), "fatherId" => 22));

        if (count($eventos) == 0) {
            return false;
        }

        return $eventos[0]->getConfig()["active"];
    }

    private function asignarResponsablePorCondicion($ejecucionFlujoId, $config) {
        $userId = null;
        $condiciones = $config["condiciones"];

        foreach($condiciones as $condicion) {
            $value = $this->registroService->valueByFieldAndFlow($condicion["fieldId"], $ejecucionFlujoId);
            if ($value == null) {
                continue;
            }

            if ($condicion["condicion"] == "Es igual a" && $condicion["valor"] == $value["valor"]) {
                $userId = $condicion["user"];
                break;
            } else if($condicion["condicion"] == "Diferente a" && $condicion["valor"] != $value["valor"]) {
                $userId = $condicion["user"];
                break;
            }
        }


        return $userId;
    }

    private function finAnticipado($ejecucionPaso) {
        $eventos = $this->em->getRepository(PasoEventoVersion::class)->findBy(array("paso_version_id" => $ejecucionPaso->getPasoVersionId(), "fatherId" => 5));
        $finalizar = false;

        if (count($eventos) == 0) {
            return $finalizar;
        }

        $condiciones = $this->homologateOldConditionsNextStep($eventos[0]->getConfig()["condiciones"]);
        $values = array();
        foreach($condiciones as $condicion) {
            if(!isset($values[$ejecucionPaso->getEjecucionFlujo()->getId()])) {
                $values[$ejecucionPaso->getEjecucionFlujo()->getId()] = array();
            }

            if ($this->checkCondition($condicion["config"], $ejecucionPaso->getEjecucionFlujo()->getId(), $values[$ejecucionPaso->getEjecucionFlujo()->getId()])) {
                $finalizar = true;
                break;
            }
        }

        return $finalizar;
    }

    private function siguientePasoPorCondicion($ejecucionPaso) {
        $eventos = $this->em->getRepository(PasoEventoVersion::class)->findBy(array("paso_version_id" => $ejecucionPaso->getPasoVersionId(), "fatherId" => 4));
        $siguientePasoVersion = null;

        if (count($eventos) == 0) {
            return $siguientePasoVersion;
        }

        $pasoId = null;
        $condiciones = $this->homologateOldConditionsNextStep($eventos[0]->getConfig()["condiciones"]);
        $values = array();
        foreach($condiciones as $condicion) {
            if(!isset($values[$ejecucionPaso->getEjecucionFlujo()->getId()])) {
                $values[$ejecucionPaso->getEjecucionFlujo()->getId()] = array();
            }

            if ($this->checkCondition($condicion["config"], $ejecucionPaso->getEjecucionFlujo()->getId(), $values[$ejecucionPaso->getEjecucionFlujo()->getId()])) {
                $pasoId = $condicion["paso_id"];
                break;
            }
        }

        if ($pasoId != null) {
            $paso = $this->em->getRepository(Paso::class)->findOneById($pasoId);
            $result = $this->em->getRepository(PasoVersion::class)
                ->findBy(array("flujo_trabajo_version_id" => $ejecucionPaso->getPasoVersion()->getFlujoTrabajoVersionId(), "numero" => $paso->getNumero()));

            if(count($result) > 0) {
                $siguientePasoVersion = $result[0];
            }
        }
        
        return $siguientePasoVersion;
    }

    private function homologateOldConditionsNextStep($conditions) {
        $newConditions = array();

        foreach($conditions as $condition) {
            if(isset($condition["config"]) && isset($condition["config"]["condition"])) {
                $newConditions[] = $condition;
            } else {
                $newCondition = array();
                $newCondition["id"] = $condition["id"];
                $newCondition["paso_id"] = $condition["paso_id"];
                $newCondition["numero"] = $condition["numero"];
                $newCondition["descripcion"] = $condition["descripcion"];

                $config = array();
                $config["condition"] = "and";

            
                $rule = array();
                $rule["fieldId"] = $condition["fieldId"];
                $rule["name"] = $condition["name"];
                $rule["operator"] = $this->getOperatorFromText($condition["condicion"]);
                $rule["value"] = $condition["valor"];

                $rules = array();
                $rules[] = $rule;

                $config["rules"] = $rules;


                $newCondition["config"] = $config;

                $newConditions[] = $newCondition;
            }
        }

        return $newConditions;
    }

    private function getOperatorFromText($textOperator) {
        return ($textOperator === "Es igual a")? "=" : "!=";
    }

    private function checkCondition($config, $flujoTrabajoVersionId, $values) {
        $totalTrue = 1;

        $rules = $config["rules"];
        if($config["condition"] == "and") {
            $totalTrue = count($rules);
        }

        $countTrue = 0;
        foreach($rules as $rule) {
            $check = false;
            if(isset($rule["condition"])) {
                $check = $this->checkCondition($rule, $flujoTrabajoVersionId, $values);
            } else {
                $value = $this->getValueByFlujoTrabajoVersionId($flujoTrabajoVersionId, $rule["fieldId"], $values);

                if($value == null) {}
                else if ($rule["operator"] == "=" && $rule["value"] == $value["valor"]) {
                    $check = true;
                } else if($rule["operator"] == "!=" && $rule["value"] != $value["valor"]) {
                    $check = true;
                }
            }

            if($check) {
                $countTrue++;

                if($countTrue == $totalTrue) {
                    break;
                }
            } else if($config["condition"] == "and") {
                break;
            }
        }
        return $countTrue == $totalTrue;
    }

    private function getValueByFlujoTrabajoVersionId($flujoTrabajoVersionId, $fieldId, $values) {
        $value = null;
        
        if(isset($values[$fieldId])) {
            $value = $values[$fieldId];
        } else {
            $value = $this->registroService->valueByFieldAndFlow($fieldId, $flujoTrabajoVersionId);
            $values[$fieldId] = $value;
        }

        return $value;
    }

    public function cambiarResponsable($ejecucionPasoId, $usuarioId, $comment) {
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        if ($ejecucionPaso == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "La ejecución paso con id " . $ejecucionPasoId . " no fue encontrado");
        }

        $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);
        if ($usuario == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "No existe el usuario con el id " . $usuarioId . " para ser asignado como responsable");
        }

        $ejecucionPaso->setUsuarioResponsable($usuario);

        $messagesc = 'Motivo reasignación de paso: '.$comment;
        $nombreCompletos = $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2();
        $this->saveComments->create($messagesc, $ejecucionPaso->getId(), $usuario->getId(), $usuario->getLogin(), $nombreCompletos, "reasignar_paso");

        $this->em->persist($ejecucionPaso);
        $this->em->flush(); 

        return array("response"=> array("Se reasignó correctamente el responsable"));
    }

    public function asignarResponsableVistoBueno($ejecucionPasoId, $usuarioId) {
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        if ($ejecucionPaso == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "La ejecución paso con id " . $ejecucionPasoId . " no fue encontrado");
        }

        $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);
        if ($usuario == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "No existe el usuario con el id " . $usuarioId . " para ser asignado como responsable del visto bueno");
        }

        $ejecucionPaso->setUsuarioResponsableVistoBueno($usuario);

        $this->em->persist($ejecucionPaso);
        $this->em->flush(); 

        return array("response"=> array("Se asignó correctamente el usuario para dar el visto bueno"));
    }


    public function returnStep($pasoActual, $userId, $accion, $PasoDevolucion, $badDevolucion,$comentario) {
        $ejecucionPasoActual = $this->em->getRepository(EjecucionPaso::class)->findOneById($pasoActual);
        $ejecucionPasoDevolucion = $this->em->getRepository(EjecucionPaso::class)->findOneById($PasoDevolucion);

        if ($ejecucionPasoActual == null or  $ejecucionPasoDevolucion == null ) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "La ejecución paso  no fue encontrado");
        }

        if($userId == 'Not_user'){
            $userId = $ejecucionPasoDevolucion->getUsuarioResponsableId();
        }

        $usuario = $this->em->getRepository(Usuario::class)->findOneById($userId);

        if ($usuario == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "No existe el usuario con el id " . $userId . " para ser asignado como responsable de la devolucion");
        }
        $ejecucionPasoDevolucion->setUsuarioResponsable($usuario);
        if($accion == 'Reiniciar'){
            $ejecucionPasoDevolucion->setEstado(WorkFlowConstant::FLOW_ACTIVE);
            $ejecucionPasoDevolucion->setDevolucion('Reiniciar');
            $ejecucionPasoActual->setEstado(WorkFlowConstant::STEP_RETURNED);
        }else if ($accion == 'Continuar'){
            $ejecucionPasoDevolucion->setEstado(WorkFlowConstant::FLOW_ACTIVE);
            $ejecucionPasoDevolucion->setDevolucion('Continuar');
            $ejecucionPasoActual->setEstado(WorkFlowConstant::STEP_RETURNED);
        }    
        $this->em->persist($ejecucionPasoActual);
        $this->em->persist($ejecucionPasoDevolucion);
        $this->em->flush(); 

        $messagesc = 'Motivo devolucion de paso: '.$comentario;
        $nombreCompletos = $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2();

        $this->saveComments->create($messagesc, $ejecucionPasoDevolucion->getId(), $usuario->getId(), $usuario->getLogin(), $nombreCompletos, "devolucion_paso");

        return array("response"=> array("Se realizo la devolucion del paso de manera correcta!"));
    }

    public function aprobarVistoBueno($ejecucionPasoId, $approved, $message) {
        if ($approved != "accept" && $approved != "return") {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "La aprobación del visto bueno es incorrecta, vuelve a intentarlo");
        }

        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        if ($ejecucionPaso == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "La ejecución paso con id " . $ejecucionPasoId . " no fue encontrado");
        }

        if ($approved == "accept") {
            $ejecucionPaso->setFechaFin(new \DateTime(date("Y-m-d H:i")));
            $ejecucionPaso->setEstado(WorkFlowConstant::STEP_COMPLETED);

            $properties = $ejecucionPaso->getTempProperties();
            $radicado = $properties["radicado"];
            $interrumpir = $properties["interrumpir"];
            $responsableSiguientePasoId = $properties["responsableSiguientePasoId"];

            $registro = $this->em->getRepository(Registro::class)->findBy(array("radicado" => $radicado))[0];

            // notificar remitente visto bueno
            $pasoVersion = $this->em->getRepository(PasoVersion::class)->findOneById($ejecucionPaso->getPasoVersion()->getId());
            $this->notificar($ejecucionPaso, $pasoVersion, $registro, 8);

            return $this->completarPasoActual($ejecucionPaso, $interrumpir, $registro, $responsableSiguientePasoId);
        } else if ($approved == "return") {
            if ($message == "") {
                throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "El mensaje es obligatorio cuando no se aprueba el visto bueno");
            }

            $ejecucionPaso->setFechaFin(new \DateTime(date("Y-m-d H:i")));
            $ejecucionPaso->setEstado(WorkFlowConstant::STEP_ACTIVE);
            $this->em->persist($ejecucionPaso);
            $this->em->flush();

            $usuario = $this->tokenStorage->getToken()->getUser();

            $nombreCompleto = $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2();

            $this->saveComments->create($message, $ejecucionPasoId, $usuario->getId(), $usuario->getLogin(), $nombreCompleto, "visto_bueno");

            return array("response"=> array("El paso se devolvió correctamente"));
        }        
    }

    public function calcularFechaVencimiento($fechaInicio, $duracion) {
        $fechaVencimiento = clone $fechaInicio;
        $fechaVencimiento->modify('+' . $duracion . ' minute');
        return $fechaVencimiento;
    } 

    public function aplazarFecha($ejecucionPasoId, $fecha) {
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        if ($ejecucionPaso == null) {
            throw new ExecutionException(Response::HTTP_PRECONDITION_FAILED, "La ejecución paso con id " . $ejecucionPasoId . " no fue encontrado");
        }

        $fechaVencimiento = $ejecucionPaso->getFechaVencimiento();
        $currentDate = new \DateTime(date("Y-m-d H:i"));
        $aplazarFecha = new \DateTime($fecha);
        $date = (array) date_diff($currentDate, $fechaVencimiento);

        $mYear = $date["y"] * 525600;
        $mMonth = $date["m"] * 43800;
        $mDay = $date["d"] * 1440;
        $mHour = $date["h"] * 60;
        $mMinute = $date["i"];

        $minutes = $mYear + $mMonth + $mDay + $mHour + $mMinute;

        $aplazarFecha->modify('+' . $minutes . ' minute');

        $ejecucionPaso->setFechaVencimiento($aplazarFecha);
        $this->em->persist($ejecucionPaso);
        $this->em->flush();

        return array("response"=> array("Se aplazó la tarea correctamente"));       
    }

    public function pasoRemitente($ejecucionPasoId) {
        $pasos = $this->em->getRepository(EjecucionPaso::class)->findBy(array("ejecucion_paso_id_siguiente" => $ejecucionPasoId), array("id" => "DESC"));
        $pasoRemitente = array();
        if (count($pasos) > 0) {
            $usuarioId = $pasos[0]->getUsuarioResponsableId();
            $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);
            $pasoRemitente["id"] = $pasos[0]->getId();
            $pasoRemitente["nombre"] = $usuario->getNombre1() . " " . $usuario->getNombre2();
            $pasoRemitente["fecha"] = $pasos[0]->getFechaFin()->format("d-m-Y h:i A");
        }

        return array("response"=> $pasoRemitente);       
    }

    public function summaryStep($ejecucionFlujoId) {
        $summarySteps = $this->em->getRepository(EjecucionPaso::class)->summaryStep($this->em, $ejecucionFlujoId);
        return array("response"=> $summarySteps);       
    }

    public function totalSteps($ejecucionFlujoId) {
        $totalSteps = $this->em->getRepository(EjecucionPaso::class)->totalSteps($this->em, $ejecucionFlujoId)[0];
        return array("response"=> $totalSteps);       
    }
}
