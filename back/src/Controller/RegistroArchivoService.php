<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RegistroArchivoService
{

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $login
     *
     * @return registroformularioStandard
     */
    public function get(Request $request)
    {
        if ($request->query->get('page')) {
            $page = $request->query->get('page');
        }else{
            $page = 1;
        }
        $query = $request->query->get('query');
        $orderBy = $request->query->get('order');
        $itemsPerPage = $request->attributes->get('_items_per_page');
        $registro = $this->em->getRepository(Registro::class)->findOneById($request->attributes->get('id'));
        $user = $this->tokenStorage->getToken()->getUser();

        $files = $this->em->getRepository(Archivo::class)->findFiles($this->em, $query, $page, $orderBy, $itemsPerPage, $registro, $user);
        return ($files);
    }
}
