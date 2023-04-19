<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class FormByDocumentalEstructureIdService
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
     * @param Request $request
     *
     * @return Usuario
     */
    public function get(Request $request): array
    {
        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findBy(array("id" => $request->attributes->get("id")))[0];
        $formulario = $estructuraDocumental->getFormulario();

        return array('formulario'=>$formulario);
    }
}
