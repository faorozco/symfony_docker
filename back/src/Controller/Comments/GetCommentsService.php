<?php

namespace App\Controller\Comments;

use App\Entity\CommentPaso;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

/**
 * Undocumented class
 */
class GetCommentsService
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
        $data = json_decode($request->getContent());

        if(
        isset($data->{'idPaso'})
        ){
            $comments = $this->em->getRepository(CommentPaso::class)->findBy(
                array("ejecucion_paso_id" => $data->{'idPaso'}),
                array('fecha' => 'ASC')
            );
            return $comments;
        } else {
            return array(["response" => "Fallo al enviar comentarios"]);
        }


    }
}
