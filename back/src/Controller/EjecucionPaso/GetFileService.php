<?php

namespace App\Controller\EjecucionPaso;

use App\Entity\Archivo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

/**
 * Undocumented class
 */
class GetFileService
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
            $files = $this->em->getRepository(Archivo::class)->findFilesFlujo($this->em,$data->{'idPaso'});
            return $files;
        } else {
            return array(["response" => "Fallo al enviar comentarios"]);
        }


    }
}
