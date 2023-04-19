<?php

namespace App\Controller;

use App\Entity\Formato;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RegistroFormatoService
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $login
     *
     * @return formatos
     */
    public function get(Request $request)
    {
        if ($request->query->get('page')) {
            $page = $request->query->get('page');
        } else {
            $page = 1;
        }
        $query = $request->query->get('query');
        $orderBy = $request->query->get('order');
        $itemsPerPage = $request->attributes->get('_items_per_page');
        $registroId = $request->attributes->get('id');

        $formatos = $this->em->getRepository(Formato::class)->findByRegistroId($query, $page, $orderBy, $itemsPerPage, $registroId);

        return ($formatos);
    }
}
