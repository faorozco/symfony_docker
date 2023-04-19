<?php

namespace App\Controller\Eventos;

use App\Entity\Eventos;
use App\Entity\PasoEvento;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class ListEventsPasosService
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
    public function Response(Request $request)
    
    {
        $data = json_decode($request->getContent());
        $paso_id = $data->{"paso_id"};
        $events_paso =  $this->em->getRepository(PasoEvento::class)->findBy(["paso_id" => $paso_id], ['fatherId' => 'ASC']);
        $events =  $this->em->getRepository(Eventos::class)->findBy(["estado_id" => 1], ['id' => 'ASC']);
        $events_result = [];
                
        if (isset($events)) {
            if(isset($events_paso)&& sizeof($events_paso) > 0){
                foreach ($events_paso as $event_paso){
                    foreach ($events as $key => $event){
                        if($event_paso->getFatherId() == $event->getFatherId() && $event_paso->getEventoId() == $event->getId()){
                            array_push($events_result,array(
                                "id" => $event->getId(),
                                "active" => true,
                                "disabled" => false,
                                "estadoId" => $event->getEstadoId(),
                                "component" => $event->getComponent(),
                                "father" => $event->getFather(), 
                                "fatherId" => $event->getFatherId(),
                                "icon" => $event->getIcon(),
                                "name" => $event->getName(),
                            ));
                            unset($events[$key]);
                        }else if($event_paso->getFatherId() == $event->getFatherId()){
                            array_push($events_result,array(
                                "id" => $event->getId(),
                                "active" => false,
                                "disabled" => true,
                                "estadoId" => $event->getEstadoId(),
                                "component" => $event->getComponent(),
                                "father" => $event->getFather(), 
                                "fatherId" => $event->getFatherId(),
                                "icon" => $event->getIcon(),
                                "name" => $event->getName(),
                            ));
                            unset($events[$key]);
                        }

                    } 
                }    
            }
            foreach ( $events as $event){
                array_push($events_result,array(
                    "id" => $event->getId(),
                    "active" => false,
                    "disabled" => false,
                    "estadoId" => $event->getEstadoId(),
                    "component" => $event->getComponent(),
                    "father" => $event->getFather(), 
                    "fatherId" => $event->getFatherId(),
                    "icon" => $event->getIcon(),
                    "name" => $event->getName(),
                ));
            }
            return $events_result;
        } else {
            return array("response" => "Error consultando los eventos");
        }
    }
}
