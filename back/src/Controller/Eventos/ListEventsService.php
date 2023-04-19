<?php

namespace App\Controller\Eventos;

use App\Entity\Eventos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class ListEventsService
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
    public function Duplicate(Request $request)
    {
        //consulto el formulario que se quiere duplicar
        $events = $this->em->getRepository(Eventos::class)->findBy(["estado_id" => 1], ['father' => 'DESC', 'name' => 'ASC']);

        if (isset($events)) {
            return $events;
        } else {
            return array("response" => "Error consultando los eventos");
        }
    }
}
