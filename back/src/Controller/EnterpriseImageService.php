<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Utils\GestorImagenes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class EnterpriseImageService
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
    public function Upload(Request $request): Empresa
    {
        $imagenEmpresa= $request->files->get("imagen");
        $empresa = $this->em->getRepository(Empresa::class)->findOneById($request->attributes->get("id"));
        if (null !== $imagenEmpresa) {
            $gestorImagenEmpresa = new GestorImagenes();
            $empresa=$gestorImagenEmpresa->uploadFile($empresa, $imagenEmpresa, $_ENV['IMAGE_LOCATION']);
            $this->em->persist($empresa);
        }
        return $empresa;
    }
}
