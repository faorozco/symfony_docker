<?php

namespace App\Controller;

use App\Entity\PasoEventoVersion;
use App\Entity\PlantillaVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class PlantillasPasoVersionService
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
        $pasoEventoVersion = $this->em->getRepository(PasoEventoVersion::class)->findOneBy(array("paso_version_id" => $request->attributes->get("id"), "evento_id" => "32"));
        if (isset($pasoEventoVersion)) {
            $plantillaId = $pasoEventoVersion->getConfig()["plantilla_id"];
            $plantillas = $this->em->getRepository(PlantillaVersion::class)->findBy(array("id" => $plantillaId));
            return $plantillas;
        } else {
            return array();
        }
    }
}
