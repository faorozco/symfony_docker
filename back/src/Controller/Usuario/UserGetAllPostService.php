<?php

namespace App\Controller\Usuario;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;

/**
 * Undocumented class
 */
class UserGetAllPostService
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
    public function post(Request $request)
    {
        $data = json_decode($request->getContent());
        $login = $data->{"login"};
        $estado = $data->{"estado"};
        $bloqueo = $data->{"bloqueo"};
        $sesion = $data->{"sesion"};
        $page = $data->{"page"};
        $pageSize = $data->{"pageSize"};
        $orden = $data->{"orden"};  
        $users = $this->em->getRepository(Usuario::class)->findUsersPost($login, $estado, $bloqueo, $sesion, $page, $pageSize,$orden);

        if (isset($users)) {
            return $users;
        } else {
            return array("response" => "No se encontraron listas");
        }
    }
}
