<?php
namespace App\Utils;

use App\Entity\Sede;
use App\Entity\Cargo;
use App\Entity\Ciudad;
use App\Entity\Proceso;
use App\Entity\Tercero;
use Port\Csv\CsvReader;
use App\Entity\Contacto;
use App\Entity\TipoContacto;
use Port\Steps\StepAggregator;
use Port\Doctrine\DoctrineWriter;
use App\Entity\EstructuraDocumental;
use Port\Steps\Step\ValueConverterStep;
use Port\ValueConverter\DateTimeValueConverter;
use Port\ValueConverter\StringToObjectConverter;
use Port\Steps\Step\ConverterStep;

class EntityImport
{
    private $_entidad;
    private $_reader;
    private $_em;

    public function __construct($entidad, $entityManager)
    {
        //Crear el lector de la entidad
        $this->entidad = $entidad;
        $this->em = $entityManager;
        switch ($this->entidad) {
            case "Tercero":
                $this->entidadRepository = $this->em->getRepository(Tercero::class);
                $this->ciudadRepository = $this->em->getRepository(Ciudad::class);
                break;
            case "Contacto":
                $this->entidadRepository = $this->em->getRepository(Contacto::class);
                $this->ciudadRepository = $this->em->getRepository(Ciudad::class);
                $this->tipoContactoRepository = $this->em->getRepository(TipoContacto::class);
                $this->terceroRepository = $this->em->getRepository(Tercero::class);
            case "Proceso":
                $this->procesoRepository = $this->em->getRepository(Proceso::class);
                $this->sedeRepository = $this->em->getRepository(Sede::class);
                break;
            case "Cargo":
                $this->procesoRepository = $this->em->getRepository(Cargo::class);                
                break;
            case "TablaRetencion":
                $this->estructuraDocumentalRepository = $this->em->getRepository(EstructuraDocumental::class);                
                break;
            default:
                break;
        }
    }

    public function Import($request)
    {
        //capturar el archivo que se quiere procesar
        $fileToImport = $request->files->get("archivo");
        //verificar que sea de tipo .csv
        if (isset($fileToImport)) {
            $clientOriginalName = $request->files->get('archivo')->getClientOriginalName();
            $filename = explode(".", $clientOriginalName);
            if ($filename[1] == "csv") {
                // Create and configure the reader
                $file = new \SplFileObject($fileToImport);
                $csvReader = new CsvReader($file);

                // Tell the reader that the first row in the CSV file contains column headers
                $csvReader->setHeaderRowNumber(0);

                // Create the workflow from the reader
                $this->workflow = new StepAggregator($csvReader);
                self::EntityConverter();
                self::DateTimeConverter();
                // Create a writer: you need Doctrine’s EntityManager.
                $doctrineWriter = new DoctrineWriter($this->em, 'App:' . $this->entidad);
                $doctrineWriter->disableTruncate();
                $this->workflow->addWriter($doctrineWriter);

                // Process the workflow
                $this->workflow->process();
                return array("result" => array("response" => "Archivo importado existosamente!"));
            } else {
                return array("result" => array("response" => "Archivo a importar no tiene extensión CSV"));
            }
        } else {
            return array("result" => array("response" => "Debe seleccionar un archivo en formato CSV"));
        }
    }

    public function EntityConverter()
    {
        $step = new ValueConverterStep();
        switch ($this->entidad) {
            case "Tercero":
                $clean =  new ConverterStep([
                    function($item) {
                            return $this->cleanProperty($item,'ciudad');
                     }
                ]);
                $this->workflow->addStep($clean);
                $this->workflow->addStep($step);
                break;
            case "Contacto":
                $clean =  new ConverterStep([
                    function($item) {
                        $item = $this->cleanProperty($item,'ciudad');
                        $item = $this->cleanProperty($item,'tipoContacto');
                        $item =  $this->cleanProperty($item,'tercero');
                        return $item;
                     }
                ]);
                $this->workflow->addStep($clean);
                $this->workflow->addStep($step);
                break;
            case "Proceso":
                $sedeConverter = new StringToObjectConverter($this->sedeRepository, 'id');
                $step->add('[sede]', $sedeConverter);

                $this->workflow->addStep($step);
                break;
            case "TablaRetencion":
                $clean =  new ConverterStep([
                    function($item) {
                        return $this->cleanProperty($item,'sede');
                     }
                ]);
                $this->workflow->addStep($clean);
                $this->workflow->addStep($step);
                break;
        }
    }

    public function cleanProperty($item,$property){
            switch($property){
                case "ciudad":
                    $item['ciudad_id'] = $item['ciudad'];
                    unset($item['ciudad']);
                    break;
                case "tipoContacto":
                    $item['tipo_contacto_id'] = $item['tipoContacto'];
                    unset($item['tipoContacto']);
                    break;    
                case "tercero":
                    $item['tercero_id'] = $item['tercero'];
                    unset($item['tercero']);
                    break; 
                case "sede":
                    $item['sede_id'] = $item['sede'];
                    unset($item['sede']);
                    break;      
                case "estructuraDocumental":
                    $item['estructura_documental_id'] = $item['estructuraDocumental'];
                    unset($item['estructuraDocumental']);
                    break;                        
            }

            return $item;
    }

    public function DateTimeConverter()
    {
        $converter = new DateTimeValueConverter('Y-m-d H:i:s');
        $step = new ValueConverterStep();
        switch ($this->entidad) {
            case "EstructuraDocumental":
                $step->add('[fecha_version]', $converter);
                $this->workflow->addStep($step);
                break;
            case "TablaRetencion":
                $step->add('[inicio_vigencia]', $converter);
                $this->workflow->addStep($step);
                break;
        }
    }
}