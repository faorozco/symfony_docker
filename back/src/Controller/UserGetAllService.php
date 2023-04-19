<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;

/**
 * Undocumented class
 */
class UserGetAllService
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
        $query = $request->query->get('query');
        $users = $this->em->getRepository(Usuario::class)->findUsersNameSystem($query);

        if (isset($users)) {
            return $users;
        } else {
            return array("response" => "No se encontraron listas");
        }
    }
}
