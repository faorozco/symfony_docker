<?php

namespace App\Utils;

use App\Entity\Usuario;
use Port\Csv\CsvWriter;
use App\Utils\TextUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataExport
{
    private $_data;

    public function __construct($data)
    {
        //Crear el lector de la entidad
        $this->data = $data;
    }

    public function Export()
    {
        $i = 0;
        $writer = new CsvWriter();
        $writer->setStream(fopen('php://temp', 'r+'));
        $writer->setCloseStreamOnFinish(false);
        // Create the workflow from the reader
        if (isset($this->data)) {
            foreach ($this->data as $row) {
                $i = 0;
                $resumen = "";
                $plainRow = array();
                $plainRow["id"] = $row->getId();
                $plainRow["fechahora"] = $row->getFechaHora()->format("Y-m-d h:i:s");
                $detalleResumen = json_decode($row->getResumen(), true);
                foreach ($detalleResumen as $detalle => $valor) {
                    if ($i === 0) {
                        $resumen .= $detalle . ":" . $valor;
                        $i = 1;
                        // first index
                    } else {
                        $resumen .= " | " . $detalle . ":" . $valor;
                    }
                }
                $plainRow["autor"] = $row->getUsuario()->getNombre1() . " " . $row->getUsuario()->getNombre2() . " " . $row->getUsuario()->getApellido1() . " " . $row->getUsuario()->getApellido2();
                $plainRow["contenido"] = $resumen;
                $writer->writeItem($plainRow);
            }
            $stream = $writer->getStream();
            rewind($stream);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=export_" . date("Ymdhis") . ".csv");
            header('Content-Transfer-Encoding: binary');
            $contents = rtrim(stream_get_contents($stream), PHP_EOL);
            exit($contents);
        } else {
            return array("response" => "Entidad a exportar no existe");
        }
    }

    public function ExportArray($em)
    {
        $writer = new CsvWriter(",");
        $writer->setStream(fopen('php://temp', 'r+'));
        $writer->setCloseStreamOnFinish(false);
        // Create the workflow from the reader
        if (isset($this->data)) {
            $oldPlainRowHeader = array();
            foreach ($this->data as $row) {
                $plainRowHeader = array();
                $plainRow = array();
                $plainRowHeader["id"] = "ID";
                $plainRow["id"] = $row["id"];
                $plainRowHeader["fechahora"] = "Fecha/Hora";
                $plainRow["fechahora"] = $row["fechaHora"];
                $plainRowHeader["autor"] = "Autor";
                $usuario = $em->getRepository(Usuario::class)->findOneById($row["usuarioId"]);
                $plainRow["autor"] = $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2();
                $detalleResumen = array();
                if (null !== $row["resumen"])
                    $detalleResumen = $row["resumen"];
                foreach ($detalleResumen as $detalle => $valor) {
                    $plainRowHeader[TextUtils::slugifyWithUnderscore(trim($detalle))] = trim($detalle);
                    $plainRow[TextUtils::slugifyWithUnderscore(trim($detalle))] = !is_array($valor) ? $valor : implode(" ", $valor);
                }
                if (count(array_diff($plainRowHeader, $oldPlainRowHeader)) != 0 || count(array_diff($oldPlainRowHeader, $plainRowHeader)) != 0) {
                    $writer->writeItem($plainRowHeader);
                    $oldPlainRowHeader = $plainRowHeader;
                }
                $writer->writeItem($plainRow);
            }
            $stream = $writer->getStream();
            rewind($stream);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=export_" . date("Ymdhis") . ".csv");
            header('Content-Transfer-Encoding: binary');
            $contents = rtrim(stream_get_contents($stream), PHP_EOL);
            exit($contents);
        } else {
            return array("response" => "Entidad a exportar no existe");
        }
    }

    public function exportExcel($em, $formularioName) {
        if (isset($this->data)) {
            $this->phpExcelObject = new Spreadsheet();
            $this->phpExcelObject->setActiveSheetIndexByName('Worksheet');
            $sheetIndex = $this->phpExcelObject->getActiveSheetIndex();
            $this->phpExcelObject->removeSheetByIndex($sheetIndex);
            $oldVersion = 0;
            $hoja = 0;
            $index = 0;
            foreach ($this->data as $row) {  
                $plainRowHeader = array();
                $plainRow = array();
                $plainRowHeader["r_id"] = "ID";
                $plainRow["r_id"] = $row["id"];
                $plainRowHeader["r_radicado"] = "Radicado";
                $plainRow["r_radicado"] = $row["radicado"];
                $plainRowHeader["r_fechahora"] = "Fecha radicado";
                $plainRow["r_fechahora"] = $row["fechaHora"];
                $plainRowHeader["r_sede"] = "Sede";
                $plainRow["r_sede"] = $row["sede"];
                $plainRowHeader["r_usuario"] = "Usuario";
                $usuario = $em->getRepository(Usuario::class)->findOneById($row["usuarioId"]);
                $plainRow["r_usuario"] = $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2();
                $plainRowHeader["r_correspondencia"] = "Correspondencia";
                $plainRow["r_correspondencia"] = $row["correspondencia"];
                $plainRowHeader["r_consecutivo"] = "Consecutivo";
                if ($row["correspondencia"]== 'N / A') {
                    $plainRow["r_consecutivo"] = '';
                } else {
                    $plainRow["r_consecutivo"] = $row["consecutivo"];
                }
                $detalleResumen = array();
                $currentVersion = $row["version"];
                if (null !== $row["resumen"])
                    $detalleResumen = $row["resumen"];
                foreach ($detalleResumen as $detalle => $valor) {
                    $plainRowHeader[TextUtils::slugifyWithUnderscore(trim($detalle))] = trim($detalle);
                    $plainRow[TextUtils::slugifyWithUnderscore(trim($detalle))] = !is_array($valor) ? $valor : implode(" ", $valor);
                }
                if ($oldVersion != $currentVersion) {
                    $this->addSheet($hoja, $currentVersion);
                    $objWorkSheet = $this->phpExcelObject->getActiveSheet($hoja);
                   
                    $index = 1;
                    $objWorkSheet->fromArray(array_values($plainRowHeader), null, 'A' . $index);
                    $hoja++;
                    $index++;

                    $oldVersion = $currentVersion;
                }
                $objWorkSheet->fromArray(array_values($plainRow), null, 'A' . $index);
                $index++;
            }
            
            ob_start();
            $writer = new Xlsx($this->phpExcelObject);
            $writer->save('php://output');
            $archivo = ob_get_contents();
            ob_end_clean();

            $fileName = 'REGISTROS_' . TextUtils::slugify($formularioName) . '-' . date("Ymdhis") . '.xls';
            $fileLocation = $_ENV['PUBLIC_TMP_LOCATION'] . $fileName;
            file_put_contents($fileLocation, $archivo);
            //$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        
            return $fileName;

        }
        
    }

    public function addSheet($hoja, $version) {
        $nombreHoja = "V-" . $version;
        $this->phpExcelObject->createSheet($hoja)
            ->setTitle(substr($nombreHoja, 0, 30));
        $this->phpExcelObject->setActiveSheetIndexByName($nombreHoja);
    }
}
