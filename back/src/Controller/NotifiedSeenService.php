<?php

namespace App\Controller;

use App\Entity\Notificado;
use Datetime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class NotifiedSeenService
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
        $notificado = $this->em->getRepository(Notificado::class)->findOneBy(array("id" => $request->attributes->get("id"), "visto" => null));
        $notificadoComentario = $this->em->getRepository(Notificado::class)->findOneBy(array("id" => $request->attributes->get("id"), "comentario" => true));
        if (isset($notificadoComentario)) {
            $notificadoComentario->setComentario(null);
            $this->em->persist($notificadoComentario);
            $this->em->flush();
        }
        if (isset($notificado)) {
            $notificado->setVisto(new Datetime());
            $this->em->persist($notificado);
            $this->em->flush();
            return array("response" => array("message" => "Visto"));
        } else {
            return array("response" => array("message" => "Ya fue marcado como Visto"));
        }
    }
}
