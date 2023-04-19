<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\EntityExport;

/**
 * Undocumented class
 */
class ProcesoExportService
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
        $this->entidad = "Proceso";

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function Export(Request $request)
    {
        $procesoExport = new EntityExport($this->entidad, $this->em);
        $procesoExport->Export();
    }
}
