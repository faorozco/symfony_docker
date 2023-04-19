<?php

namespace App\Controller;

use App\Entity\Registro;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\StickerGenerator;


/**
 * Undocumented class
 */
class StickerViewerService
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
        $registro = $this->em->getRepository(Registro::class)->findOneById($request->attributes->get("id"));
        if (isset($registro)) {
            return StickerGenerator::Get($this->em, $registro);            
        }  else {
            return array("response" => "Registro de formulario no encontrado");
        }
    }
}
