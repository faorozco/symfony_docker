<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\Empresa;
use App\Utils\Gdrive;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class DownloadFileService
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
     * @return Empresa
     */
    public function Get(Request $request)
    {
        $archivo = $this->em->getRepository(Archivo::class)->findOneById($request->attributes->get("id"));
        if (isset($archivo)) {
            if ($archivo->getIdentificador() != "") {
                $client = new Gdrive();
                $clientGDocument = $client->getClient();
                $service = new \Google_Service_Drive($clientGDocument);
                $result = $client->downloadPhysicalFile($request, $service, $archivo->getIdentificador(),$archivo->getNombre());
                return $result;
            } else if ($archivo->getIdentificador() == "") {
                return array("response" => array("message" => "Archivo con identificador " . $request->attributes->get("id") . " no tiene un identificador de almacenamiento válido"));
            }
        } else if (!isset($archivo)) {
            return array("response" => array("message" => "Archivo con identificador " . $request->attributes->get("id") . " no existe"));
        }
    }
}
