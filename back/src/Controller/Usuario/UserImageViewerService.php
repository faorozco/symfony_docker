<?php

namespace App\Controller\Usuario;

use App\Entity\Usuario;
use App\Utils\FileViewer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class UserImageViewerService
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
    public function Get(Request $request)
    {
        $usuario = $this->em->getRepository(Usuario::class)->findOneById($request->attributes->get("id"));
        if ($usuario->getImagen() != "") {
            $imagenUsuario = new FileViewer();
            $imagenUsuario->Get($usuario->getImagen());
        }
    }
}
