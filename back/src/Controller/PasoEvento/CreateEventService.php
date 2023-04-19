<?php

namespace App\Controller\PasoEvento;
use App\Entity\PasoEvento;
use App\Entity\Paso;
use App\Entity\Eventos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class CreateEventService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function Create(Request $request)
    {
        $data = json_decode($request->getContent());
        $paso = $this->em->getRepository(Paso::class)->findOneById($data->{'paso_id'});
        $evento = $this->em->getRepository(Eventos::class)->findOneById($data->{'event_id'});
        $config = $data->{'config'};
        $fatherId = $evento->getFatherId();
        if (isset($paso) && isset($evento) && isset($config)) {
            $pasoEvento = $this->em->getRepository(PasoEvento::class)->findOneBy(["paso_id"=>$data->{'paso_id'},"evento_id"=>$data->{'event_id'}]);
            $fatherReq = $this->em->getRepository(PasoEvento::class)->findOneBy(["paso_id"=>$data->{'paso_id'},"fatherId"=>$fatherId]);
            if(isset($fatherReq) && $data->{'event_id'} != $fatherReq->getEventoId()){
                throw new \Exception('No se puede guardar la configuracion evento, tiene dependencias de configuracion');
            }
            if(isset($pasoEvento)){
                $pasoEvento->setConfig((array) $config);
                $pasoEvento->setFatherId($fatherId);
                $this->em->persist($pasoEvento);
                $this->em->flush();
            }else{
                $event = new PasoEvento();
                $event->setPaso($paso);
                $event->setEvento($evento);
                $event->setFatherId($fatherId);
                $event->setConfig((array) $config);
                $this->em->persist($event);
                $this->em->flush();
            }
        } else {
            return array(["response" => "Fallo al crear el evento"]);
        }

    }
}
