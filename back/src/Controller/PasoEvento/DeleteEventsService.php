<?php

namespace App\Controller\PasoEvento;

use App\Entity\PasoEvento;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class DeleteEventsService
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
    public function Delete(Request $request)
    {
        $data = json_decode($request->getContent());
        if (isset($data->{'paso_id'}) && isset($data->{'event_id'})) {
        $pasoEvento = $this->em->getRepository(PasoEvento::class)->findOneBy(["paso_id"=>$data->{'paso_id'},"evento_id"=>$data->{'event_id'}]);
        if(isset($pasoEvento)){

            $this->em->remove($pasoEvento);

            $this->em->flush();

            return ([array("delete" => "true")]);
        }else{
            return ([array("delete" => "false")]);
        }
        } else {
            return ([array("delete" => "false")]);
        }
    }
}
