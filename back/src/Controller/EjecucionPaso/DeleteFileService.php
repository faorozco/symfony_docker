<?php

namespace App\Controller\EjecucionPaso;

use App\Entity\Archivo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class DeleteFileService
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
    public function delete(Request $request)
    {
        $data = json_decode($request->getContent());

        if(
        isset($data->{'archivoOrigen'})
        ){
            $files = $this->em->getRepository(Archivo::class)->findBy(
                array("archivo_origen" => $data->{'archivoOrigen'},"estado_id" => '1'),
                array('fecha_version' => 'ASC')
            );
            foreach ($files as $file) {
                $file->setEstadoId(0);
                $this->em->persist($file);
                $this->em->flush();
            }
             

            return array(["response" => "true"]);
        } else {
            return array(["response" => "Fallo al eliminar archivo"]);
        }


    }
}
