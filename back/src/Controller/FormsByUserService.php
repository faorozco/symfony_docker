<?php

namespace App\Controller;

use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class FormsByUserService
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
    public function get($query): array
    {
        $resultado = [];
        $usuario = $this->tokenStorage->getToken()->getUser();
        $resultado = $this->em->getRepository(Formulario::class)
            ->getByUser($query, $usuario, $this->em);

        return $resultado;
    }
}
