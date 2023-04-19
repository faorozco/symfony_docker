<?php

namespace App\Controller\Comments;

use App\Entity\CommentPaso;
use App\Entity\EjecucionPaso;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

/**
 * Undocumented class
 */
class SaveCommentsService
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
    public function create($comentario, $ejecucionPasoId, $idUser, $user, $nombreCompleto, $typeComent)
    {
        $saveComentarioEjecucion = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        $saveComentarioEjecucion->setComment('1');
        $comment = new CommentPaso();
        $comment->setComentario($comentario);
        $comment->setEjecucionPasoId($ejecucionPasoId);
        $comment->setFecha(new \DateTime());
        $comment->setIdUser($idUser);
        $comment->setNombreCompleto($nombreCompleto);
        $comment->setTypeComment($typeComent);
        $comment->setUser($user);
        $this->em->persist($comment);
        $this->em->persist($saveComentarioEjecucion);
        $this->em->flush();
    }
}
