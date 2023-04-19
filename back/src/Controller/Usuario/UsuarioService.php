<?php

namespace App\Controller\Usuario;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class UsuarioService
{
    private $_em;
    private $_result;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->result = array();

    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function cargarUsuariosGrupo(Request $request)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $grupoId = $request->attributes->get("id");
        $filter = $request->query->get("filter");
        $usuarios = $this->em->getRepository(Usuario::class)->findUsersGroup($this->em, $grupoId, $filter);

        if(count($usuarios) > 0) {
           return $usuarios;    
        } else {
            return array("response" => "No hay formularios asociados");
        }  
    }

    public function cargarUsuariosGruposFormularioVersion(Request $request)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $formularioId = $request->attributes->get("id");
        $filter = $request->query->get("filter");
        $usuarios = $this->em->getRepository(Usuario::class)->findByGroupsFormId($this->em, $formularioId, $filter);

        if(count($usuarios) > 0) {
           return $usuarios;    
        } else {
            return array("response" => "No hay formularios asociados");
        }  
    }

    public function cargarUsuariosSystema(Request $request)
    {
        $filter = $request->query->get("filter");
        $usuarios = $this->em->getRepository(Usuario::class)->findUsersSystem($filter);

        if(count($usuarios) > 0) {
           return $usuarios;    
        } else {
            return array("response" => "No se encontraron usuarios");
        }  
    }
}
