<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Utils\EntityImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class EstructuraDocumentalImportService
{
    private $_em;
    private $_entidad;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->entidad = "EstructuraDocumental";
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function Import(Request $request)
    {
        $fileToImport = $request->files->get("archivo");
        //verificar que sea de tipo .csv
        if (isset($fileToImport)) {
            $clientOriginalName = $request->files->get('archivo')->getClientOriginalName();
            $filename = explode(".", $clientOriginalName);
            if ($filename[1] == "csv") {
                //Se analiza el archivo recibido
                $result = $this->ValidarCodigosRepetidos();
                //Si existe algún còdigo de directorio padre o hijo  repetido se abota el proceso de importaciòn y se informa del problema.
                if ($result == 0) {
                    $estructuraDocumentalImport = new EntityImport($this->entidad, $this->em);
                    return $estructuraDocumentalImport->Import($request);
                } else {
                    return array(
                        "result" => array("response" => "Archivo no se puede importar. Contiene códigos de directorio repetidos")
                    );
                }
            } else {
                return array("result" => array("response" => "Archivo a importar no tiene extensión CSV"));
            }
        } else {
            return array("result" => array("response" => "Debe seleccionar un archivo en formato CSV"));
        }
    }

    public function ValidarCodigosrepetidos()
    {
        $file = fopen($_FILES["archivo"]["tmp_name"], "r");
        $contador = 0;
        $codigos = array();
        while (($csvrow = fgetcsv($file, 4000, ";")) !== false) {
            if ($contador > 0) {
                $csvarray = explode(",", $csvrow[0]);
                if ($csvarray[2] != 0){
                    $codigos["codigo_directorio"][] = $csvarray[2];
                }
            }
            $contador = 1;
        }
        return $this->em->getRepository(EstructuraDocumental::class)->checkDuplicateCodigosDirectorio($codigos["codigo_directorio"]);
    }
}
