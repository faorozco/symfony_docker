<?php

namespace App\Controller;

use App\Entity\CampoFormulario;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class FormFieldLoaderService
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
    public function get($request)
    {
        $resultado = array();
        $resultado = $this->em->getRepository(CampoFormulario::class)
            ->FormFieldLoader($request);

        return $resultado;
    }
}
