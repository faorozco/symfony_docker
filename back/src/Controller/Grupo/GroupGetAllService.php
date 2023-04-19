<?php

namespace App\Controller\Grupo;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Grupo;

/**
 * Undocumented class
 */
class GroupGetAllService
{
    private $_em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }
    
    public function get(Request $request)
    {
        $page = $request->query->get('page');
        $queryString = $request->query->get('query');
        $itemsPerPage = '10';
        $id = $request->attributes->get('id');
        $estado = $request->query->get('estado');
        $orderBy = 'ASC';

        $grupos = $this->em->getRepository(Grupo::class)->findAllGroups($queryString, $page, $itemsPerPage, $estado, $orderBy);

        if (isset($grupos)) {
            return $grupos;
        } else {
            return array("response" => "No se encontraron grupos");
        }
    }
}
