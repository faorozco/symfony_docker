<?php
namespace App\Utils;

use App\Utils\Gdrive;

class GestorImagenes
{

    public function uploadFile($entidad, $file, $folder)
    {
        $files_saved = array();

        $file_mime = $file->getMimeType();
        $file_org = $file->getClientOriginalName();
        $client = new Gdrive();
        $clientGDocument = $client->getClient();
        $service = new \Google_Service_Drive($clientGDocument);
        $fileMetadata = new \Google_Service_Drive_DriveFile(
            array(
                'name' => $file_org,
                'parents' => array($folder),
            )
        );
        if ($entidad->getImagen() != "" && $entidad->getImagen()!=$_ENV["DEFAULT_AVATAR"]) {
            try{
                $service->files->delete($entidad->getImagen());
            }catch (\Exception $e) {
                $entidad->getImagen() != "";
            };
            
        }

        $content = file_get_contents($_FILES["imagen"]["tmp_name"]);
        $file_saved = $service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $file_mime,
            'uploadType' => 'multipart',
            'fields' => 'id',
        )
        );
        $entidad->setImagen($file_saved->id);
        return $entidad;
    }
}
