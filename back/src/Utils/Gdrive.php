<?php
namespace App\Utils;

define('APPLICATION_NAME_GDOCUMENT', 'Cliente gDocument');
define('CREDENTIALS_PATH_GDOCUMENT', dirname(__FILE__) . '/../../config/gdrivecredentials/credenciales/credentials.json');
define('CLIENT_SECRET_PATH_GDOCUMENT', dirname(__FILE__) . '/../../config/gdrivecredentials/client_secret.json');
define('SCOPES_GDOCUMENT', implode(' ', array(
    \Google_Service_Drive::DRIVE)
));

class Gdrive
{
    /*
     * Create a new Google Drive Client.
     */
    public function getClient()
    {
        $client = new \Google_Client();
        $client->setApplicationName(APPLICATION_NAME_GDOCUMENT);
        $client->setScopes(SCOPES_GDOCUMENT);
        $client->setAuthConfig(CLIENT_SECRET_PATH_GDOCUMENT);
        $client->setAccessType('offline');

        $credentialsPath = self::expandHomeDirectory(CREDENTIALS_PATH_GDOCUMENT);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }

        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }
    /*
     * Open Google Drive home directory
     */
    public static function expandHomeDirectory($path)
    {
        // $homeDirectory = getenv('HOME');
        // if (empty($homeDirectory)) {
        //     $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        // }
        return str_replace('~', "", $path);
    }
    /*
     * List elements contained in specific folder
     */
    public function listFilesInFolder($service, $folderId)
    {
        $pageToken = null;
        //   do {
        $response = $service->files->listFiles(
            array(
                'q' => "'" . $folderId . "' in parents",
                'spaces' => 'drive',
                'pageToken' => $pageToken,
                'fields' => 'nextPageToken, files(id, name)',
            )
        );
        //   foreach ($response->files as $file) {
        //           printf("Found file: %s (%s)\n", $file->name, $file->id);
        //   }
        //   } while ($pageToken != null);
        return $response->files;
    }
    /*
     * Download a specific file
     */
    //Descargar archivo
    public function downloadFile($service, $fileId)
    {
        $fileMetada = $service->files->get($fileId);
        $file = $service->files->get($fileId, array('alt' => 'media'));
        $content = $file->getBody()->getContents();

        header("Cache-Control: no-cache private");
        header("Content-Description: File Transfer");
        header("Content-disposition: inline; filename='" . $fileMetada["name"] . "'");
        header("Content-Type: " . $fileMetada["mimeType"]);
        header("Content-Transfer-Encoding: binary");
        header("Pragma: no-cache");
        header("Expires: 0");
        exit($content);
    }

    /*
     * Download a specific file to filesystem
     */
    //Descargar archivo
    public function downloadPhysicalFile($request, $service, $fileId, $nombreArchivo = null)
    {
        $fileMetada = $service->files->get($fileId);
        $file = $service->files->get($fileId, array('alt' => 'media'));
        $content = $file->getBody()->getContents();
        if (null === $nombreArchivo) {
            $nombreArchivo = $fileMetada["name"];
        }
        $fileLocation = $_ENV['PUBLIC_TMP_LOCATION'] . $nombreArchivo;

        file_put_contents($fileLocation, $content);
        
        $schema = $request->server->get("SYMFONY_DEFAULT_ROUTE_SCHEME");
        if ($schema == "") {
            $schema = "https";
        }
        $baseurl = $schema . '://' . $request->getHttpHost() . $request->getBasePath();
        return array("response" => array("location" => $baseurl . "/tmp/" . rawurlencode($nombreArchivo)));
    }

    //Leer archivo
    public function readFile($service, $fileId)
    {
        $fileMetada = $service->files->get($fileId);
        $file = $service->files->get($fileId, array('alt' => 'media'));
        $content = $file->getBody()->getContents();
        return array("archivo" => $content, "fileMetada" => $fileMetada);
    }

    public function searchFiles($service, $folder = "", $nombre_archivo = "")
    {
        $pageToken = null;
        do {
            $response = $service->files->listFiles(
                array(
                    // 'q'=> "mimeType='image/gif' and name contains 'default'",
                    'q' => "name='$nombre_archivo'",
                    'spaces' => 'drive',
                    'pageToken' => $pageToken,
                    'fields' => 'nextPageToken, files(id, name)',
                )
            );
            foreach ($response->files as $file) {
                printf("Found file: %s (%s)\n", $file->name, $file->id);
            }
        } while ($pageToken != null);
    }

    //Save files locally
    public static function saveFile($client, $service, $fileId)
    {
        $optParams = array("spaces" => "drive");
        $fileMetada = $service->files->get($fileId);
        $file = $service->files->get($fileId, array(
            'alt' => 'media'));
        $content = $file->getBody()->getContents();
        $fileroute = date("Y") . "/" . date("m") . "/" . $fileMetada["name"];
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/uploads/files/' . date("Y") . "/" . date("m") . "/")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/uploads/files/' . date("Y") . "/" . date("m") . "/", 0777, true);
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/uploads/files/' . $fileroute, $content);
        $datafile = array("filename" => $fileMetada["name"], "routefile" => $fileroute);
        return $datafile;
    }

    public static function moveFile($client, $service, $folderBackupId, $fileId)
    {
        $emptyFileMetadata = new \Google_Service_Drive_DriveFile();
        // Retrieve the existing parents to remove
        $file = $service->files->get($fileId, array('fields' => 'parents'));
        // $previousParents = join(',', $file->parents);
        // Move the file to the new folder
        $file = $service->files->update($fileId, $emptyFileMetadata, array(
            'addParents' => $folderBackupId,
            'removeParents' => $file->parents,
            'fields' => 'id, parents'));
    }

    public function removeFile($service, $fileId)
    {
        try {
            $service->files->delete($fileId);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
        return true;
    }

    public function createFolder($service, $folderName, $parentFolder)
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => $folderName,
            'parents' => [$parentFolder],
            'mimeType' => 'application/vnd.google-apps.folder'));
        $file = $service->files->create($fileMetadata, array(
            'fields' => 'id'));
        return $file->id;

    }
}
