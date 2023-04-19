<?php

namespace App\Controller;

use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RegistryByUserService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function Get(Request $request)
    {
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $orderBy = $request->query->get('order');
        $itemsPerPage = $request->attributes->get('_items_per_page');

        //Consultar el usuario
        $user = $this->tokenStorage->getToken()->getUser();
        //Consultar que registros ha creado el usuario
        $registros = $this->em->getRepository(Registro::class)->findRegistrosByUser($this->em, $query, $page, $orderBy, $itemsPerPage, $user);
        return ($registros);
    }
}
