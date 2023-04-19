<?php
namespace App\Utils;

use App\Utils\MyStreamWriter;
use Port\Steps\StepAggregator;
use App\Utils\DoctrineReaderCustom;

class EntityExport
{
    private $_entidad;
    private $_reader;
    private $_em;

    public function __construct($entidad, $entityManager)
    {
        //Crear el lector de la entidad
        $this->entidad = $entidad;
        $this->reader = new DoctrineReaderCustom($entityManager, 'App:' . $entidad);
        $this->em = $entityManager;
    }

    public function Export()
    {
        // Create the workflow from the reader
        if (isset($this->reader)) {
            $workflow = new StepAggregator($this->reader);

            $writer = new MyStreamWriter(fopen('php://temp', 'r+'));
            $writer->setCloseStreamOnFinish(false);

            $workflow->addWriter($writer);
            // Process the workflow
            $workflow->process();
            $stream = $writer->getStream();
            rewind($stream);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=$this->entidad.csv");
            header('Content-Transfer-Encoding: binary');

            $entityColumns = $this->em->getClassMetadata("App\Entity\\" . $this->entidad)->getFieldNames();
            $entityColumns = implode(",", $entityColumns) . PHP_EOL;
            $entityColumns = self::replaceAssociationNames($entityColumns);
            $contents = $entityColumns . rtrim(stream_get_contents($stream), PHP_EOL);
            exit($contents);
        } else {
            return array("response" => "Entidad a exportar no existe");
        }
    }

    public function replaceAssociationNames($entityColumns)
    {
        //reemplazar ciudadId por ciudad en entidad Tercero
        $entityColumns = str_replace("ciudad_id", "ciudad", $entityColumns);
        $entityColumns = str_replace("tercero_id", "tercero", $entityColumns);
        $entityColumns = str_replace("tipo_contacto_id", "tipoContacto", $entityColumns);
        $entityColumns = str_replace("sede_id", "sede", $entityColumns);

        return $entityColumns;
    }
}
