<?php

namespace App\Controller\Grupo;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Grupo;

/**
 * Undocumented class
 */
class GroupOnlyGetService
{
    private $_em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }
    
    public function get(Request $request)
    {
        $id = $request->attributes->get("id");

        $users = $this->em->getRepository(Grupo::class)->findOneId($id);

        if (isset($users)) {
            return $users;
        } else {
            return array("response" => "No se encontraron usuarios");
        }
    }
}
