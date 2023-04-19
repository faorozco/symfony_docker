<?php

namespace App\Controller;

use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class PlantillasFormularioVersionService
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
    public function get(Request $request)
    {
        $registro = $this->em->getRepository(Registro::class)->findOneBy(array("id" => $request->attributes->get("id")));
        if (isset($registro)) {
            //consulto el formulario relacionado a este registro
            $formularioVersion = $registro->getFormularioVersion();
            //consulto las plantillas relacionadas a ese formulario
            $plantillas = $formularioVersion->getPlantillasVersion();
            return $plantillas;
        } else {
            return array();
        }
    }
}
