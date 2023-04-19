<?php

namespace App\Utils;

use ZipArchive;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ZipManager
{
    public static function ReadContent($file): array
    {
        $za = new ZipArchive();

        $za->open($file);

        for ($i = 0; $i < $za->numFiles; $i++) {
            $stat = $za->statIndex($i);
            if (strstr( $stat["name"], '/')!=false) {
                return $hasSubfolder = array("hasSubfolder" => true);
            }

            $contenidoZip[] = basename($stat['name']);
        }

        return $contenidoZip;
    }

    public static function CreateContainer($filename)
    {
        $options = new Archive();
        $options->setSendHttpHeaders(true);
        $options->setContentDisposition('attachment; filename=' . $filename);
        $options->setHttpHeaderCallback('header');
        $zip = new ZipStream($filename, $options);
        return $zip;
    }

    public static function WriteContent($zipFileName, $temporalmasivo)
    {

        $zipLocation = 'tmp/' . $zipFileName;
        $zip = new ZipArchive();
        $zip->open($zipLocation, ZipArchive::CREATE);
        $options = array('add_path' => "filesexported/", 'remove_all_path' => true);
        $zip->addGlob($temporalmasivo . '/*.csv', 0, $options);
        $zip->close();
        return $zipLocation;
    }

    public static function getZippedFile($file, $filename)
    {
        $za = new ZipArchive();

        $za->open($file);

        return $za->getFromName($filename);
    }
}
