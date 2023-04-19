<?php
namespace App\Utils;

use App\Utils\Gdrive;

class FileViewer
{
    public function Get($fileId)
    {
        $client = new Gdrive();
        $clientGDocument = $client->getClient();
        $service = new \Google_Service_Drive($clientGDocument);
        $client->downloadFile($service, $fileId);

    }
}
