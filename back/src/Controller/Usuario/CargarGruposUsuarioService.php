<?php

namespace App\Controller\Usuario;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class CargarGruposUsuarioService
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
    public function get(Request $request)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $grupoId = $request->attributes->get("id");
        $filter = $request->query->get("filter");
        $grupos = $this->em->getRepository(Usuario::class)->findGruopsUser($this->em, $grupoId, $filter);

        if(count($grupos) > 0) {
           return $grupos;    
        } else {
            return array("response" => 'no hay grupos asociados');
        }  
    }

}
