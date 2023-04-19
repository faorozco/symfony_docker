<?php

namespace App\Controller\PasoEvento;

use App\Entity\PasoEvento;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class ConfigEventsService
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
    public function Search(Request $request)
    {
        $data = json_decode($request->getContent());
        if (isset($data->{'paso_id'}) && isset($data->{'event_id'})) {
        $pasoEvento = $this->em->getRepository(PasoEvento::class)->findOneBy(["paso_id"=>$data->{'paso_id'},"evento_id"=>$data->{'event_id'}]);
        if(isset($pasoEvento)){
            return (array("result" => (["config" => $pasoEvento->getConfig(),
                                    "id" => $pasoEvento->getId(),
                                    "paso_id" => $pasoEvento->getPasoId(),
                                    "evento_id" => $pasoEvento->getEventoId()])));
        }else{
            return ([array("response" => "Sin configurar")]);
        }
        } else {
            return ([array("response" => "Sin configurar")]);
        }
    }
}
