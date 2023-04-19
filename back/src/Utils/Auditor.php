<?php

namespace App\Utils;

use App\Entity\Auditoria;
use App\Utils\TextUtils;

class Auditor
{
    public function post($em, $arrayEntity, $entity, $usuario)
    {

        $operacion = "CREACION";
        $valor_anterior = null;
        $valor_actual = $arrayEntity;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $estado_id = "1";
        $auditoria = new Auditoria();
        $auditoria->setUsuario($usuario);
        $auditoria->setUsername($usuario->getLogin());
        $auditoria->setEntidad(TextUtils::getClassName(get_class($entity)));
        $auditoria->setOperacion($operacion);
        $auditoria->setValorAnterior($valor_anterior);
        $auditoria->setValorActual($valor_actual);
        $auditoria->setIpCliente($ip_cliente);
        $auditoria->setFecha(new \DateTime());
        $auditoria->setEntidadId($entity->getId());
        $auditoria->setEstadoId(1);

        $em->persist($auditoria);
        $em->flush();
        return;
    }

    public function put($em, $entidad, $usuario, $cambios)
    {
        $anterior = array();
        $actual = array();
        foreach ($cambios as $key => $cambio) {
            $anterior[$key] = $cambio[0];
            $actual[$key] = $cambio[1];
        }
        if (count($actual) > 0) {
            $operacion = "ACTUALIZACION";
            $valor_anterior = $anterior;
            $valor_actual = $actual;
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $estado_id = "1";
            $auditoria = new Auditoria();
            $auditoria->setUsuario($usuario);
            $auditoria->setUsername($usuario->getLogin());
            $auditoria->setEntidad(TextUtils::getClassName(get_class($entidad)));
            $auditoria->setOperacion($operacion);
            $auditoria->setValorAnterior($valor_anterior);
            $auditoria->setValorActual($valor_actual);
            $auditoria->setIpCliente($ip_cliente);
            $auditoria->setFecha(new \DateTime());
            $auditoria->setEntidadId($entidad->getId());
            $auditoria->setEstadoId(1);

            $em->persist($auditoria);
            $em->flush();
        }
        return;
    }

    public function delete($em, $arrayEntity, $entity, $usuario)
    {

        $operacion = "BORRADO";
        $valor_anterior = $arrayEntity;
        $valor_actual = null;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $estado_id = "1";
        $auditoria = new Auditoria();
        $auditoria->setUsuario($usuario);
        $auditoria->setUsername($usuario->getLogin());
        $auditoria->setEntidad(TextUtils::getClassName(get_class($entity)));
        $auditoria->setOperacion($operacion);
        $auditoria->setValorAnterior($valor_anterior);
        $auditoria->setValorActual($valor_actual);
        $auditoria->setIpCliente($ip_cliente);
        $auditoria->setFecha(new \DateTime());
        $auditoria->setEntidadId($entity->getId());
        $auditoria->setEstadoId(1);

        $em->persist($auditoria);
        $em->flush();
        return;
    }

    public function login($em, $usuario)
    {
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $estado_id = "1";
            $auditoria = new Auditoria();
            $auditoria->setUsuario($usuario);
            $auditoria->setUsername($usuario->getLogin());
            $auditoria->setEntidad("Usuario");
            $auditoria->setOperacion("Inicio de sesión");
            $auditoria->setIpCliente($ip_cliente);
            $auditoria->setFecha(new \DateTime());
            if($usuario->getTokenValidAfter() != null){
            $valor_anterior = array( 'login' => "Ultima vez que inicio sesión: ". $usuario->getTokenValidAfter()->format("Y-m-d H:i:s"));
            }else{
                $valor_anterior = array( 'login' => "primer inicio de sesión");
            }    
            $valor_actual = array( 'login' =>"Inicio de sesión:  ". $auditoria->getFecha()->format("Y-m-d H:i:s"));
            $auditoria->setValorAnterior($valor_anterior);
            $auditoria->setValorActual($valor_actual);
            $auditoria->setEntidadId($usuario->getId());
            $auditoria->setEstadoId(1);
            $em->persist($auditoria);
            $em->flush();
    }

    public static function registerAction($em, $entidad = null, $usuario, $valor_anterior = null, $valor_actual = null, $action)
    {

        $operacion = $action;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $estado_id = "1";
        $auditoria = new Auditoria();
        $auditoria->setUsuario($usuario);
        $auditoria->setUsername($usuario->getLogin());
        if (TextUtils::getClassName(get_class($entidad)) != "") {
            $auditoria->setEntidad(TextUtils::getClassName(get_class($entidad)));
        }
        $auditoria->setOperacion($operacion);
        $auditoria->setValorAnterior($valor_anterior);
        $auditoria->setValorActual($valor_actual);
        $auditoria->setIpCliente($ip_cliente);
        $auditoria->setFecha(new \DateTime());
        if (TextUtils::getClassName(get_class($entidad)) != "") {
            $auditoria->setEntidadId($entidad->getId());
        }
        $auditoria->setEstadoId(1);
        $em->persist($auditoria);
        $em->flush();
        return;
    }
}
