<?php
namespace App\Utils;

use App\Utils\FileUtils;
use App\Utils\ZipManager;
use Port\Csv\CsvWriter;

class ArrayExport
{
    private $_data;

    public function __construct($files, $filename)
    {
        //Crear el lector de la entidad
        $this->files = $files;
        $this->filename = $filename;
    }

    public function Export($request)
    {
        // $stream = null;
        // $zipFile = ZipManager::CreateContainer($this->filename);
        $filestozip = array();
        $temporalmasivo = "tmp/masivo" . date("Ymdhis") . "/";
        foreach ($this->files as $name => $file) {
            $i = 0;
            $writer = new CsvWriter(",");
            if (!is_dir($temporalmasivo)) {
                mkdir($temporalmasivo);
            }
            $filenametozip = $temporalmasivo . $name . ".csv";
            $writer->setStream(fopen($filenametozip, 'w'));
            $writer->setCloseStreamOnFinish(false);
            foreach ($file as $value) {
                $writer->writeItem($value);
            }
            //$stream = $writer->getStream();
            $writer->finish();
            //rewind($stream);
            $filestozip[] = $filenametozip;
        }
        $zipLocation = ZipManager::WriteContent($this->filename, $temporalmasivo);
        FileUtils::deleteDirectory($temporalmasivo);
        $schema = $request->server->get("SYMFONY_DEFAULT_ROUTE_SCHEME");
        if ($schema == "") {
            $schema = "https";
        }
        $baseurl = $schema . '://' . $request->getHttpHost() . $request->getBasePath();
        return array("response" => array("location" => $baseurl . "/" . $zipLocation));
    }
}
