<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Utils\Gdrive;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class RemoteFileService
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
    public function get($request): array
    {
        $query = $request->query->get('query');
        //consultar los archivos ubicados en una ruta en especial de Google Drive
        $files = array();
        $client = new Gdrive();
        $clientGDocument = $client->getClient();
        $service = new \Google_Service_Drive($clientGDocument);
        $result = $client->listFilesInFolder($service, $_ENV["GDRIVECLIENT_FILE_LOCATION"]);
        foreach ($result as $file) {
            if ($query != "") {
                if (strpos($file->name, $query) !== false) {
                    $files[] = array("id" => $file->id, "name" => $file->name);
                }
            } else {
                $files[] = array("id" => $file->id, "name" => $file->name);
            }

        }
        //Obtener un arreglo con idetificador y nombre de archivo

        return $files;
    }
}
