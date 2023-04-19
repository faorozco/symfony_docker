<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Utils\TextUtils;

class EstructuraDocumentalXlsVersionService
{
    private $_em;

    public $cuadroNegro;
    public $cuadroBlanco;
    public $cuadroCheck;


    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

        $this->cuadroNegro = '■';
        $this->cuadroBlanco = '□';
        $this->cuadroCheck = '√';

        $this->version = '';


    }

    public function get($nodo, $version)
    {

        $this->version = $version;

        $nodoPrincipal = array();
        if($nodo !== ''){
            $iterableResult = $this->getNodoPrincipal($nodo);

            
            while (($row = $iterableResult->next()) !== false) {

                $item = array_values($row);

                $value = $item[0];

                $nodoPrincipal[] = $value;

            }

        }
        $arbolHijos = $this->arbol($nodo);

        $arbolFinal = array_merge($nodoPrincipal, $arbolHijos);

        $fondoArray = $this->getNombreFondo();

        $nombreFondo = $fondoArray['descripcion'];
        $version = $fondoArray['version'];
        $fechaVersion = $fondoArray['fecha_version']->format("Y-m-d");

        $xls = $this->generarExcel($arbolFinal, $nombreFondo, $version, $fechaVersion);

        return $xls;

        // return array('response' => array(
        //     'nombre' => 'TRD_'.trim($nombreFondo) . '.xlsx',
        //     'data' => 'data:application/vnd.ms-excel;base64,' . \base64_encode($xls),
        // ));
    }

    public function arbol($parent, $arbol = '')
    {
        if (!is_array($arbol)) {
            $arbol = array();
        }

        $iterableResult = $this->getChildrens($parent);

        while (($row = $iterableResult->next()) !== false) {

            $item = array_values($row);

            $value = $item[0];

            $arbol[] = $value;
            if ($value['codigo_directorio'] !== '0' && $value['codigo_directorio'] !== '') {
                $arbol = $this->arbol($value['codigo_directorio'], $arbol);
            }

        }

        return $arbol;
    }

    public function getNombreFondo()
    {

        $queryBuilder = $this->em->createQueryBuilder();
        $entityResults = $queryBuilder
            ->select("estructura.descripcion, estructura.version, estructura.fecha_version")
            ->from('App\\Entity\\EstructuraDocumentalVersion', 'estructura')
            ->where("estructura.codigo_directorio_padre = :codigo")
            ->andWhere("estructura.version = :version")
            ->setParameter('codigo', "-1")
            ->setParameter('version', $this->version)
            ->orderBy('estructura.peso', 'ASC')
            ->getQuery()
            ->execute();

        if (empty($entityResults)) {
            return array();
        }

        return $entityResults[0];
    }

    public function getChildrens($parent)
    {
        $queryBuilder = $this->em->createQuery("select estructura.codigo_directorio,
                                                        '' as d1,
                                                        trim(estructura.descripcion) as descripcion,
                                                        '' as d2,
                                                        '' as d3,
                                                        '' as d4,
                                                        '' as codigo,
                                                        '' as proceso,
                                                        tablar.tipo_soporte,
                                                        tablar.tiempo_retencion_archivo_gestion,
                                                        tablar.tiempo_retencion_archivo_central,
                                                        (case when tablar.disposicion_final_borrar = '1' then 'X' else '' end),
                                                        (case when tablar.disposicion_final_conservacion_digital = '1' then 'X' else '' end),
                                                        (case when tablar.disposicion_final_microfilmado = '1' then 'X' else '' end),
                                                        (case when tablar.disposicion_final_conservacion_total = '1' then 'X' else '' end),
                                                        (case when tablar.disposicion_final_digitalizacion_microfilmacion = '1' then 'X' else '' end),
                                                        (case when tablar.disposicion_final_migrar = '1' then 'X' else '' end),
                                                        (case when tablar.disposicion_final_seleccion = '1' then 'X' else '' end),
                                                        tablar.transferencia_medio_electronico,
                                                        tablar.direccion_documentos_almacenados_electronicamente,
                                                        tablar.procedimiento_disposicion,
                                                        tablar.ley_normatividad,
                                                        estructura.id,
                                                        estructura.codigo_directorio_padre,
                                                        estructura.type
                                                    from App\\Entity\\EstructuraDocumentalVersion estructura
                                                    left join estructura.tablaRetencionVersion tablar
                                                    where estructura.codigo_directorio_padre = '{$parent}' 
                                                    and estructura.version = $this->version 
                                                    order by estructura.codigo_directorio asc, estructura.peso asc");
        $iterableResult = $queryBuilder->iterate();

        return $iterableResult;

    }

    
    public function getNodoPrincipal($parent)
    {
        $queryBuilder = $this->em->createQuery("select estructura.codigo_directorio,
                                                        '' as d1,
                                                        trim(estructura.descripcion) as descripcion,
                                                        '' as d2,
                                                        '' as d3,
                                                        '' as d4,
                                                        '' as codigo,
                                                        '' as proceso,
                                                        tablar.tipo_soporte,
                                                        tablar.tiempo_retencion_archivo_gestion,
                                                        tablar.tiempo_retencion_archivo_central,
                                                        tablar.disposicion_final_conservacion_total,
                                                        tablar.disposicion_final_conservacion_digital,
                                                        tablar.disposicion_final_microfilmado,
                                                        tablar.disposicion_final_seleccion,
                                                        '' aa,
                                                        '' bb,
                                                        '' cc,
                                                        tablar.transferencia_medio_electronico,
                                                        tablar.direccion_documentos_almacenados_electronicamente,
                                                        tablar.procedimiento_disposicion,
                                                        tablar.ley_normatividad,
                                                        estructura.id,
                                                        estructura.codigo_directorio_padre,
                                                        estructura.type
                                                    from App\\Entity\\EstructuraDocumentalVersion estructura
                                                    left join estructura.tablaRetencionVersion tablar
                                                    where estructura.codigo_directorio = '{$parent}' 
                                                    and estructura.version = $this->version 
                                                    order by estructura.codigo_directorio asc, estructura.peso asc");
        $iterableResult = $queryBuilder->iterate();

        return $iterableResult;

    }

    public function setDatosFinales($hoja, $index)
    {

        $this->phpExcelObject->setActiveSheetIndex($hoja);

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 1), 'CONVENCIONES:');


        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $this->phpExcelObject->getActiveSheet($hoja)->getStyle('A8:U'.$index)->applyFromArray($styleArray);


        

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 2), '■');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 3), '□');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 4), '√');

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 5), 'AG  =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 6), 'AC  =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 7), 'B/E =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('A' . ($index + 8), 'CD  =');

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 2), 'Serie Documental');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 2) . ':' . 'D' . ($index + 2));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 3), 'Subserie Documental');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 3) . ':' . 'D' . ($index + 3));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 4), 'Tipo Documental');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 4) . ':' . 'D' . ($index + 4));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 5), 'Archivo de Gestión');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 5) . ':' . 'D' . ($index + 5));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 6), 'Archivo Central');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 6) . ':' . 'D' . ($index + 6));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 7), 'Borrar y/o Eliminar');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 7) . ':' . 'D' . ($index + 7));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('B' . ($index + 8), 'Conservar y Digitalizar');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('B' . ($index + 8) . ':' . 'D' . ($index + 8));

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('F' . ($index + 2), 'D/M =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('F' . ($index + 3), 'CM  =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('F' . ($index + 4), 'CT  =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('F' . ($index + 5), 'M   =');
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('F' . ($index + 6), 'S   =');

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('G' . ($index + 2), 'Digitalización / Microfilmación u otros soportes');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('G' . ($index + 2) . ':' . 'H' . ($index + 2));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('G' . ($index + 3), 'Conservar y Microfilmar');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('G' . ($index + 3) . ':' . 'H' . ($index + 3));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('G' . ($index + 4), 'Conservación Total en el Archivo de Gestión, Central, Historico, o Transferencia al Archivo General Municipal, Departamental, o Nacional para Conservación Total.');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('G' . ($index + 4) . ':' . 'H' . ($index + 4));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('G' . ($index + 5), 'Migrar');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('G' . ($index + 5) . ':' . 'H' . ($index + 5));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('G' . ($index + 6), 'Selección');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('G' . ($index + 6) . ':' . 'H' . ($index + 6));

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('J' . ($index + 4), 'Firma responsable del Archivo:');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('J' . ($index + 4) . ':' . 'P' . ($index + 4));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('J' . ($index + 7), 'Firma Responsable del Proceso:');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('J' . ($index + 7) . ':' . 'P' . ($index + 7));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('J' . ($index + 10), 'Firma Responsable Secretaria General:');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('J' . ($index + 10) . ':' . 'P' . ($index + 10));

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('Q' . ($index + 5), 'Jefe y/o Coordinación del Archivo');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('Q' . ($index + 4) . ':' . 'U' . ($index + 4));
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('Q' . ($index + 5) . ':' . 'U' . ($index + 5));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('Q' . ($index + 8), '(Cargo)');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('Q' . ($index + 7) . ':' . 'U' . ($index + 7));
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('Q' . ($index + 8) . ':' . 'U' . ($index + 8));
        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue('Q' . ($index + 11), '(Cargo)');
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('Q' . ($index + 10) . ':' . 'U' . ($index + 10));
        $this->phpExcelObject->getActiveSheet($hoja)->mergeCells('Q' . ($index + 11) . ':' . 'U' . ($index + 11));

        $styleArray = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $this->phpExcelObject->getActiveSheet($hoja)->getStyle('Q' . ($index + 5) . ':' . 'U' . ($index + 5))->applyFromArray($styleArray);
        $this->phpExcelObject->getActiveSheet($hoja)->getStyle('Q' . ($index + 8) . ':' . 'U' . ($index + 8))->applyFromArray($styleArray);
        $this->phpExcelObject->getActiveSheet($hoja)->getStyle('Q' . ($index + 11) . ':' . 'U' . ($index + 11))->applyFromArray($styleArray);

        $this->phpExcelObject->getActiveSheet($hoja)->getColumnDimension('U')->setWidth(20);

    }

    public function ponerCuadroNegro($hoja, $celda)
    {


        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue($celda, $this->cuadroNegro);
        


    }
    public function ponerCuadroBlanco($hoja, $celda)
    {

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue($celda, $this->cuadroBlanco);

        

    }
    public function ponerCuadroCheck($hoja, $celda)
    {

        $this->phpExcelObject->getActiveSheet($hoja)->setCellValue($celda, $this->cuadroCheck);

        

    }

    public function generarExcel($arbol, $nombreFondo, $version, $fechaVersion)
    {
        $this->phpExcelObject = new Spreadsheet();

        $this->phpExcelObject->getDefaultStyle()
            ->getFont()
            ->setBold(false)
            ->setSize(7)
            ->setName('Arial');

        $hoja = -1;

        $index = 10;

        $indexArray = array();

        $procesoNombre = "";

        $hashProceso = array();

        foreach ($arbol as $nodo) {

            if ($nodo['codigo_directorio_padre'] == '' || strlen($nodo['codigo_directorio']) == ($_ENV['SECCION'] + $_ENV['SUBSECCION'])) {

                $index = 10;

                $hoja++;

                $indexArray[$hoja] = $index;

                if ($nodo['codigo_directorio_padre'] == '' && !isset($hashProceso[$nodo['codigo_directorio']])) {
                    $hashProceso[$nodo['codigo_directorio']] = $nodo['descripcion'];
                }

                if ($nodo['codigo_directorio_padre'] != '') {
                    $procesoNombre = $hashProceso[$nodo['codigo_directorio_padre']];
                } else {
                    $procesoNombre = $nodo['descripcion'];
                }

                $this->nuevaHoja($hoja, $nodo['descripcion'], $nodo['codigo_directorio'], $nombreFondo, $version, $fechaVersion);

                continue;
            }

            $objWorkSheet = $this->phpExcelObject->getActiveSheet($hoja);

            unset($nodo['id']);
            unset($nodo['type']);
            unset($nodo['codigo_directorio_padre']);
            if ($nodo["ley_normatividad"] != "") {
                $nodo["procedimiento_disposicion"] .= "\n\nLey o Normatividad.\n" . $nodo["ley_normatividad"];
            }
            unset($nodo['ley_normatividad']);            

            if ($nodo['codigo_directorio'] == '0') {
                $nodo['codigo_directorio'] = '';
            }

            if (strlen($nodo['codigo_directorio']) == ($_ENV['SECCION'] + $_ENV['SUBSECCION'] + $_ENV['SERIE'] + $_ENV['SUBSERIE'])) {
                $nodo['proceso'] = $procesoNombre;
            }

            $objWorkSheet->fromArray(array_values($nodo), null, 'A' . $index);

            $objWorkSheet->getStyle('U'.$index)->getAlignment()->setWrapText(true);

            if ($nodo['codigo_directorio'] == '') {

                $this->ponerCuadroCheck($hoja, 'B' . $index);
            }

            if (strlen($nodo['codigo_directorio']) == ($_ENV['SECCION'] + $_ENV['SUBSECCION'] + $_ENV['SERIE'])) {

                $this->ponerCuadroNegro($hoja, 'B' . $index);

                $objWorkSheet->getStyle('A' . $index . ':U' . $index)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('8e8e8e');
            }

            if (strlen($nodo['codigo_directorio']) == ($_ENV['SECCION'] + $_ENV['SUBSECCION'] + $_ENV['SERIE'] + $_ENV['SUBSERIE'])) {
                $this->ponerCuadroBlanco($hoja, 'B' . $index);

                $objWorkSheet->getStyle('A' . $index . ':U' . $index)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('afafaf');
            }

            $index++;

            $indexArray[$hoja] = $index;

        }

        foreach ($indexArray as $key => $value) {

            $this->setDatosFinales($key, $value);

            
        }


        $this->phpExcelObject->setActiveSheetIndexByName('Worksheet');
        $sheetIndex = $this->phpExcelObject->getActiveSheetIndex();
        $this->phpExcelObject->removeSheetByIndex($sheetIndex);

        $this->phpExcelObject->setActiveSheetIndex(0);

        ob_start();
        $writer = new Xlsx($this->phpExcelObject);
        $writer->save('php://output');
        $archivo = ob_get_contents();
        ob_end_clean();


        $fileLocation = $_ENV['PUBLIC_TMP_LOCATION'] . 'TRD_' . TextUtils::slugify($nombreFondo) . '-' . date("Ymd") . '.xls';
        file_put_contents($fileLocation, $archivo);
        
        return 'TRD_' . TextUtils::slugify($nombreFondo) . '-' . date("Ymd") . '.xls';
            


    }

    public function getTitulos()
    {

        $titulos = array('CÓDIGO', 'SERIES, SUBSERIES  Y TIPOS DOCUMENTALES', '', '', '', '', 'CÓDIGO SGI',
            'PROCESO', 'Formato (*.pdf,*csv, etc.)', 'RETENCIÓN', '',
            'DISPOSICÍON FINAL', '', '', '', '', '', '',
            'Transferencia en Medios Electrónicos',
            'Dirección (URL) para documentos almacenados electrónicamente',
            'PROCEDIMIENTOS');

        return $titulos;

    }

    public function nuevaHoja($index, $titulo, $codigo, $nombreFondo, $version, $fechaVersion)
    {

        $nombreHoja = $codigo . '-' . $titulo;

        $this->phpExcelObject->createSheet($index)
            ->setTitle(substr($nombreHoja, 0, 30));

        $this->phpExcelObject->setActiveSheetIndex($index);

        $objWorkSheet = $this->phpExcelObject->getActiveSheet($index);

        $objWorkSheet->fromArray($this->getTitulos(), null, 'A8');
        $objWorkSheet->fromArray(array('AG', 'AC', 'B/E', 'CD', 'CM', 'CT', 'D/M', 'M', 'S'), null, 'J9');

        $objWorkSheet->getStyle('A8:U8')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle('J9:R9')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $objWorkSheet->mergeCells('A8:A9');
            $objWorkSheet->mergeCells('B8:F9');
            $objWorkSheet->mergeCells('G8:G9');
            $objWorkSheet->mergeCells('H8:H9');
            $objWorkSheet->mergeCells('I8:I9');
            
            $objWorkSheet->mergeCells('U8:U9');
            
            $objWorkSheet->mergeCells('J8:K8');
            $objWorkSheet->mergeCells('L8:R8');
            $objWorkSheet->mergeCells('S8:S9');
            $objWorkSheet->mergeCells('T8:T9');

        $objWorkSheet->mergeCells('A4:C4');
        $objWorkSheet->mergeCells('A5:C5');
        $objWorkSheet->mergeCells('A6:C6');
        
        $objWorkSheet->setCellValue('D2', 'TABLA DE RETENCIÓN DOCUMENTAL');
        
        $objWorkSheet->mergeCells('D2:P2');

        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];

        $objWorkSheet->getStyle('D2:P2')->applyFromArray($styleArray);

        $objWorkSheet->getStyle('D2:P2')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle('D2:P2')
            ->getFont()->setSize(10);

        $objWorkSheet->setCellValue('A4', 'ENTIDAD PRODUCTORA');
        $objWorkSheet->setCellValue('A5', 'OFICINA PRODUCTORA');
        $objWorkSheet->setCellValue('A6', 'CÓDIGO DEPENDENCIA');

        $objWorkSheet->setCellValue('D4', $nombreFondo);
        $objWorkSheet->setCellValue('D5', $titulo);
        $objWorkSheet->setCellValue('D6', $codigo);

        $objWorkSheet->mergeCells('D4:K4');
        $objWorkSheet->mergeCells('D5:K5');

        $objWorkSheet->setCellValue('U1', '');
        $objWorkSheet->setCellValue('U2', $fechaVersion);
        $objWorkSheet->setCellValue('U3', 'Versión: ' . $version);

        $objWorkSheet->getStyle('U1')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle('U2')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $objWorkSheet->getStyle('U3')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $objWorkSheet->getStyle('U1')->applyFromArray($styleArray);
        $objWorkSheet->getStyle('U2')->applyFromArray($styleArray);
        $objWorkSheet->getStyle('U3')->applyFromArray($styleArray);
            

        $this->agregarImagenLogo($index);
    }

    public function agregarImagenLogo($index)
    {

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('../public/images/logoempresa.png');
        $drawing->setHeight(36);

        $drawing->setWorksheet($this->phpExcelObject->getActiveSheet($index));

    }
}
