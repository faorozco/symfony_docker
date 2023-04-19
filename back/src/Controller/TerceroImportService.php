<?php

namespace App\Controller;

use App\Utils\EntityImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class TerceroImportService
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
        $this->entidad = "Tercero";

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
        $terceroImport = new EntityImport($this->entidad, $this->em);
        return $terceroImport->Import($request);
    }
}
