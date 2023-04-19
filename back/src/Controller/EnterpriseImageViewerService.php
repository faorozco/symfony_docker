<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Utils\FileViewer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class EnterpriseImageViewerService
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
     * @return Empresa
     */
    public function Get(Request $request): Empresa
    {
        $empresa = $this->em->getRepository(Empresa::class)->findOneById($request->attributes->get("id"));
        if ($empresa->getImagen() != "") {
            $imagenEmpresa = new FileViewer();
            $imagenEmpresa->Get($empresa->getImagen());
        }
    }
}
