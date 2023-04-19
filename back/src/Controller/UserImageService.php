<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Utils\GestorImagenes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class UserImageService
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
    public function Upload(Request $request)
    {
        $imagenUsuario = $request->files->get("imagen");
        $usuario = $this->em->getRepository(Usuario::class)->findOneById($request->attributes->get("id"));
        if (null !== $imagenUsuario) {
            $gestorImagenUsuario = new GestorImagenes();
            $usuario = $gestorImagenUsuario->uploadFile($usuario, $imagenUsuario, $_ENV["IMAGE_USER_LOCATION"]);
            $this->em->persist($usuario);
            return $usuario;
        } else {
            return array("mensaje" => array("response" => "No seleccionó imagen"));
        }
    }
}
