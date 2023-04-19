<?php

namespace App\Controller\Usuario;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;

/**
 * Undocumented class
 */
class UserGetIdService
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
    public function get(Request $request)
    {
        $id = $request->attributes->get('id');
        $users = $this->em->getRepository(Usuario::class)->findOneForId($id);
            
        if (isset($users)) {
            return $users;
        } else {
            return array("response" => "No se encontro tus permisos");
        }
    }
}
