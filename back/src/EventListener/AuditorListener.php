<?php

namespace App\EventListener;

//Se deben agregar todas al entidades que van a tener registro de auditoria
use App\Entity\Cargo;
use App\Entity\FlujoTrabajo;
use App\Entity\Paso;
use App\Entity\Grupo;
use App\Entity\Lista;
use App\Entity\Ciudad;
use App\Utils\Auditor;
use App\Entity\Empresa;
use App\Entity\Proceso;
use App\Entity\Tercero;
use App\Entity\Registro;
use App\Entity\Usuario;
use App\Entity\Plantilla;
use App\Entity\Formulario;
use App\Entity\FormularioVersion;
use App\Entity\DetalleLista;
use App\Entity\TipoContacto;
use App\Entity\Contacto;
use App\Entity\TablaRetencion;
use App\Entity\CampoFormulario;
use App\Entity\ValorDocumental;
use App\Entity\EstructuraDocumental;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuditorListener
{

    private $tokenStorage;
    private $usuario;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    // the listener methods receive an argument which gives you access to
    // both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->instanceofEntity($entity)) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $entityManager = $args->getObjectManager();
            if( $entity instanceof Paso)
            {
                $arrayEntity = $this->calculatePasoCreate($entity);
            }else if ($this->entityWithArray($entity)) 
            {
                $arrayEntity = $entity->toArray();
            }else if( $entity instanceof Usuario)
            {
                    $arrayEntity = json_decode($serializer->serialize($entity, 'json'));
                    unset($arrayEntity->tokenValidAfter);
                    unset($arrayEntity->try);
                    unset($arrayEntity->activeSesion);
                    unset($arrayEntity->clave);
                    unset($arrayEntity->imagen);
                    unset($arrayEntity->password);
                   
             }
             else 
            {
                $arrayEntity = json_decode($serializer->serialize($entity, 'json'));
            }
            $this->usuario = $this->tokenStorage->getToken()->getUser();
            $auditor = new Auditor();
            $auditor->post($entityManager, $arrayEntity, $entity, $this->usuario);
        } else {
            return;
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
            $entity = $args->getEntity();
            $this->usuario = $this->usuario = $this->tokenStorage->getToken() == null ? null : $this->tokenStorage->getToken()->getUser();
            if ($this->instanceofEntity($entity) && null !== $this->usuario) {
                $entityManager = $args->getEntityManager();
                $uow = $entityManager->getUnitOfWork();
                $cambios = $uow->getEntityChangeSet($entity);
                if( $entity instanceof Paso){
                    $cambios = $this->calculatePaso($entity);
                }
                if( $entity instanceof Usuario){
                    unset($cambios["tokenValidAfter"]);
                    unset($cambios["try"]);
                    unset($cambios["activeSesion"]);
                    if(array_key_exists('clave', $cambios)){
                        $cambios = $this->changeKey($cambios,true,$this->usuario->getLogin());
                    }
                    if(array_key_exists('bloqueo', $cambios)){
                        $cambios = $this->changeBlock($cambios,$this->usuario->getLogin());
                    }
                }
                if(count($cambios) > 0){
                    $auditor = new Auditor();
                    $auditor->put($entityManager, $entity, $this->usuario, $cambios);  
                }else{
                    return;
                }
                              
            }else{
                return;
            }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->instanceofEntity($entity)) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer(), new PropertyNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $entityManager = $args->getObjectManager();
            $arrayEntity = json_decode($serializer->serialize($entity, 'json'));
            $this->usuario = $this->tokenStorage->getToken()->getUser();
            $auditor = new Auditor();
            $auditor->delete($entityManager, $arrayEntity, $entity, $this->usuario);
        } else {
            return;
        }
    }

    private function converTime($time,$plazo)
    {
        switch($time) { 
            case "H": { 
               $plazo = $plazo/60; 
               $time = 'Horas';
               break; 
            } 
            case "D": { 
              $plazo = $plazo/1440; 
              $time = 'Dias';
              break; 
           } 
           case "Ms": { 
            $plazo = $plazo/43800;
            $time = 'Meses';
            break; 
           } 
           case "A": { 
            $plazo = $plazo/525600;
            $time = 'Años'; 
            break; 
           } 
           default:{
            $time = 'Minutos';
           }
         }
         return [$time,$plazo];
    }

    private function changeKey($cambios,$update,$username){

        if($update){
            $cambios['clave'][1]= "La contraseña fue actualizada por el usuario: ". $username;
            $cambios['clave'][0]= " ";
        }else{
            $cambios['clave'][0] = "La contraseña fue creada por el usuario: ". $username;
        }
        return $cambios;
    }

    private function changeBlock($cambios,$username){
        if($cambios['bloqueo'][1]){
            $cambios['bloqueo'][0] = "Sin bloqueo";
            $cambios['bloqueo'][1] =  "El usuario fue bloqueado por el admin: ". $username;
        }else{
            $cambios['bloqueo'][0] = "Bloqueado";
            $cambios['bloqueo'][1] =  "El usuario fue desbloquedo por el admin: ". $username;
        }
    return $cambios;
    }

    private function entityWithArray($entity){
        if (
            $entity instanceof Formulario ||
            $entity instanceof FormularioVersion ||
            $entity instanceof CampoFormulario ||
            $entity instanceof Registro ||
            $entity instanceof Proceso ||
            $entity instanceof DetalleLista ||
            $entity instanceof FlujoTrabajo 
        ){
            return true;
        }else{
            return false;
        }
    }

    private function convertPriority($prioridad){
        switch($prioridad) { 
            case 0: { 
               $prioridad = 'Baja'; 
               break; 
            } 
            case 1: { 
                $prioridad = 'Normal'; 
              break; 
           } 
           case 2: { 
            $prioridad = 'Alta'; 
            break; 
            } 
           default:{
                $prioridad = 'Baja'; 
           }
         }
         return $prioridad;
    }

    private function instanceofEntity($entity){

        if(
        $entity instanceof CampoFormulario || $entity instanceof Cargo || $entity instanceof Ciudad ||
        $entity instanceof TipoContacto || $entity instanceof EstructuraDocumental || $entity instanceof Empresa ||
        $entity instanceof FlujoTrabajo || $entity instanceof Formulario || $entity instanceof FormularioVersion ||
        $entity instanceof Grupo || $entity instanceof Lista ||$entity instanceof DetalleLista || $entity instanceof PasoFlujo ||
        $entity instanceof Permites || $entity instanceof Plantillas || $entity instanceof Procesos ||$entity instanceof Roles ||
        $entity instanceof Sedes || $entity instanceof TablaRetencion || $entity instanceof Tercero ||
        $entity instanceof Contacto || $entity instanceof Usuario || $entity instanceof Registro
        ){
                return true;
        }
        else{
                return false;
        }
    }

    private function calculatePaso($entity){
        $cambios = $uow->getEntityChangeSet($entity);
        if(array_key_exists('plazo',$cambios) and array_key_exists('time',$cambios)){
            $tbefore = $this->converTime( $cambios['time'][0], $cambios['plazo'][0]);
            $tafter = $this->converTime( $cambios['time'][1], $cambios['plazo'][1]);
            $cambios['time'] = [$tbefore[0],$tafter[0]];
            $cambios['plazo'] = [$tbefore[1],$tafter[1]];
        }else if(array_key_exists('time',$cambios)){
            $tbefore = $this->converTime( $cambios['time'][0], 0);
            $tafter = $this->converTime( $cambios['time'][1], 0);
            $cambios['time'] = [$tbefore[0],$tafter[0]];
        }else if(array_key_exists('plazo',$cambios)){
            $tbefore = $this->converTime( $entity->getTime(), $cambios['plazo'][0]);
            $tafter = $this->converTime( $entity->getTime(), $cambios['plazo'][1]);
            $cambios['plazo'] = [$tbefore[1],$tafter[1]];
        }
        if(array_key_exists('prioridad',$cambios)){
            $cambios['prioridad'][0] = $this->convertPriority($cambios['prioridad'][0]);
            $cambios['prioridad'][1] = $this->convertPriority($cambios['prioridad'][1]);
        }
        return $cambios;
    }

    private function calculatePasoCreate($entity){
            $paso = $entity->toArray();
            $t = $this->converTime( $paso['time'], $paso['plazo']);
            $paso['time'] = $t[0];
            $paso['plazo'] = $t[1];
            $paso['prioridad'] = $this->convertPriority($paso['prioridad']);
            return $paso;
    }
}
