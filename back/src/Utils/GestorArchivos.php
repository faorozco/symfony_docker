<?php
namespace App\Utils;

use App\Entity\Carpeta;
use App\Utils\Gdrive;

class GestorArchivos
{

    public function uploadFile($em, $file, $folder, $rootFolder)
    {
        $files_saved = array();
        if (is_object($file)) {
            $file_mime = $file->getMimeType();
        } else {
            $file_mime = mime_content_type($file);
        }
        if (is_object($file)) {
            $file_org = $file->getClientOriginalName();
        } else {
            $file_org = basename($file);
        }

        $client = new Gdrive();
        $clientGDocument = $client->getClient();

        $service = new \Google_Service_Drive($clientGDocument);

        $carpeta = $em->getRepository(Carpeta::class)->findOneByDescripcion($folder);
        if (null === $carpeta) {
            $folderGdriveId = $client->createFolder($service, $folder, $rootFolder);
            $carpeta = new Carpeta();
            $carpeta->setDescripcion($folder);
            $carpeta->setIdentificador($folderGdriveId);
            $em->persist($carpeta);
            $em->flush();
        } else {
            $folderGdriveId = $carpeta->getIdentificador();
        }

        $fileMetadata = new \Google_Service_Drive_DriveFile(
            array(
                'name' => $file_org,
                'parents' => array($folderGdriveId),
            )
        );
        if (isset($_FILES["archivo"]["tmp_name"])) {
            if (is_object($file)) {
                $content = file_get_contents($_FILES["archivo"]["tmp_name"]);
            } else {
                $content = file_get_contents($file);
            }
        } else if (null !== $file->getPathName()) {
            $content = file_get_contents($file->getPathName());
        } else {
            $content = file_get_contents($file);
        }

        $file_saved = $service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $file_mime,
            'uploadType' => 'multipart',
            'fields' => 'id',
        )
        );
        return array("gDriveFileSavedID" => $file_saved->id, "carpeta" => $carpeta);
    }



    public function uploadRemoteFile($em, $fileId, $folder, $rootFolder)
    {
        $client = new Gdrive();
        $clientGDocument = $client->getClient();

        $service = new \Google_Service_Drive($clientGDocument);

        $carpeta = $em->getRepository(Carpeta::class)->findOneByDescripcion($folder);
        if (null === $carpeta) {
            $folderGdriveId = $client->createFolder($service, $folder, $rootFolder);
            $carpeta = new Carpeta();
            $carpeta->setDescripcion($folder);
            $carpeta->setIdentificador($folderGdriveId);
            $em->persist($carpeta);
            $em->flush();
        } else {
            $folderGdriveId = $carpeta->getIdentificador();
        }       

        $client->moveFile($client, $service, $folderGdriveId, $fileId);

        return array("gDriveFileSavedID" => $fileId, "carpeta" => $carpeta);
    }
}
