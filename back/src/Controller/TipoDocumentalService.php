<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class TipoDocumentalService
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
    public function get(Request $request)
    {
        $id = $request->attributes->get("id");
        //Obtener los registros donde el codigo_directorio = 0 y el type sea igual a tipo_documental
        return $this->em->getRepository(EstructuraDocumental::class)->getTipoDocumentalById($this->em, $id);
    }
}
