<?php

namespace App\Controller;

use App\Entity\TablaRetencion;
use App\Entity\EstructuraDocumental;
use App\Utils\EntityImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class TablaRetencionImportService
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
        $this->entidad = "TablaRetencion";
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
                $result = $this->ValidarIdsRepetidos();
                //Si existe algún còdigo de directorio padre o hijo  repetido se arbota el proceso de importaciòn y se informa del problema.
                if ($result == 0) {
                    $tablaRetencionImport = new EntityImport($this->entidad, $this->em);
                    $response = $tablaRetencionImport->Import($request);

                    $this->em->getRepository(EstructuraDocumental::class)->sincroniceTRD($this->em);

                    return $response;
                } else {
                    return array(
                        "result" => array("response" => "Archivo no se puede importar. Contiene identificadores de Estructura documental repetidos")
                    );
                }
            } else {
                return array("result" => array("response" => "Archivo a importar no tiene extensión CSV"));
            }
        } else {
            return array("result" => array("response" => "Debe seleccionar un archivo en formato CSV"));
        }
    }

    public function ValidarIdsRepetidos()
    {
        $file = fopen($_FILES["archivo"]["tmp_name"], "r");
        $contador = 0;
        $codigos = array();
        while (($csvrow = fgetcsv($file, 4000, ";")) !== false) {
            if ($contador > 0) {
                $csvarray = explode(",", $csvrow[0]);
                $codigos["id"][] = $csvarray[0];
            }
            $contador = 1;
        }
        return $this->em->getRepository(TablaRetencion::class)->checkDuplicateIdsEstructuraDocumental($codigos["id"]);
    }
}
