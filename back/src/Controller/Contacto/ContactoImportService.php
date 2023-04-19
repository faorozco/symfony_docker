<?php

namespace App\Controller\Contacto;

use App\Utils\EntityImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ContactoImportService
{
    private $_em;
    private $_entidad;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->entidad = "Contacto";

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function Import(Request $request)
    {
        $contactoImport = new EntityImport($this->entidad, $this->em);
        return $contactoImport->Import($request);
    }
}
