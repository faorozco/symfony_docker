<?php

namespace App\Controller\Paso;

use App\Entity\FlujoTrabajo;
use App\Entity\Paso;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class PasosByFlujoService
{
    private $_em;
    private $_result;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->result = array();

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function Get(Request $request)
    {   $flujoId = $request->attributes->get("id");
        $pasosFlujo = $this->em->getRepository(Paso::class)->findFlujo($flujoId);
        if(isset($pasosFlujo)){
            return $pasosFlujo;
        }else{
            return ([array("response" => "Sin pasos asociados")]);
        }
    }
}
