<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\CampoFormularioVersion;
use App\Entity\DetalleLista;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use App\Entity\RegistroCampo;
use App\Entity\RegistroEntidad;
use App\Entity\TipoCorrespondencia;
use App\Utils\DateTimeUtils;
use App\Utils\GestorArchivos;
use App\Utils\TextUtils;
use App\Utils\ZipManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Exceptions\FormException;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Entidad;

/**
 * Undocumented class
 */
class ProcesarEstructuraModeloVersionService
{
    private $_em;
    private $_formulario;
    private $_fileToProcess;
    private $_content;
    private $_response;
    private $_contentWithOutHeader;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function process(Request $request)
    {
        $this->response = $this->validateFormularioId($request);
        if (null === $this->response) {
            //Validar que hayan archivos relacionados.
            // De haberlo validar que existan en el archivo subido previamente
            $this->response = $this->validateFileToProcess($request);
        }
        if (null === $this->response) {
            //Validar que la cantidad de elementos sea igual a la cantidad de columnas del formularioVersion
            $this->response = $this->validateColumns($request);
        }
        if (null === $this->response) {
            $this->response = $this->validateContent($request);
        }
        if (null === $this->response) {
            $this->response = $this->processContent($request);
        }
        if (null === $this->response) {
            return array("validateFormularioId" => array("response" => array("0" => array("message" => "Procesado"))));
        } else if ($this->response != null) {
            return $this->response;
        }
    }

    private function validateFormularioId($request)
    {
        // Recibir el identificador del formularioVersion relacionado
        $formulario_version_id = $request->attributes->get("id");
        //Validamos que el identificador del formularioVersion nos devuelva un obejto de este tipo
        $this->formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($formulario_version_id);
        if (!isset($this->formularioVersion)) {
            return array("validateFormularioId" => array("response" => array("0" => array("message" => "El identificador de formulario enviado no existe"))));
        }
    }

    private function validateFileToProcess($request)
    { // Recibir el archivo a procesar
        $this->fileToProcess = $request->files->get("archivo");
        //verificamos la existencia del archivo
        if (!isset($this->fileToProcess)) {
            return array("validateFileToProcess" => array("response" => array("0" => array("message" => "No ha seleccionado un archivo"))));
        } else if (isset($this->fileToProcess)) {
            //Leer el contenido del archivo
            $this->ReadFileContent();
            //verificar si el contenido tiene archivo relacionado
            return ($this->VerifyRelatedFilesInContent());
        }
    }

    private function validateColumns($request)
    {
        $header = $this->content[0];
        $camposFormularioVersion = $this->formularioVersion->getCampoFormulariosVersion();
        foreach ($camposFormularioVersion as $campoFormularioVersion) {
            if ($campoFormularioVersion->getEstadoId() == 1 && $campoFormularioVersion->getOcultoAlRadicar() != 1) {
                $formHeader[] = $campoFormularioVersion->getCampo();
            }
        }

        $lastColumn = end($header);
        if ($lastColumn == "Fecha Archivo") {
            array_pop($header);
            array_pop($header);
            array_pop($header);
        } else {
            array_pop($header);
        }
        if ($header !== $formHeader) {
            return array("validateColumns" => array("response" => array("0" => array("message" => "La cabecera del formularioVersion no es igual que el archivo enviado"))));
        }
    }

    private function validateContent($request)
    {
        $header = $this->content[0];
        $headerWithoutFiles = $header;
        $lastColumn = end($header);
        $hasFiles = false;
        if ($lastColumn == "Fecha Archivo") {
            array_pop($headerWithoutFiles);
            array_pop($headerWithoutFiles);
            array_pop($headerWithoutFiles);
            $hasFiles = true;
        } else {
            $tipoCorrespondencia = end($headerWithoutFiles);
            array_pop($headerWithoutFiles);
        }
        // Tomar fila a fila e ir validando el contenido
        // Este contenido debe validarse segun la naturaleza del campo
        $rowNumber = 1;
        $dataBugs = array();
        foreach ($this->contentWithOutHeader as $rowWithOutHeader) {
            if ($hasFiles == true) {
                array_pop($rowWithOutHeader);
                array_pop($rowWithOutHeader);
                $tipoCorrespondenciaId = end($rowWithOutHeader);
                array_pop($rowWithOutHeader);
            } else {
                $tipoCorrespondenciaId = end($rowWithOutHeader);
                array_pop($rowWithOutHeader);
            }
            $tipoCorrespondencia = $this->em->getRepository(TipoCorrespondencia::class)->findOneById($tipoCorrespondenciaId);
            if (null === $tipoCorrespondencia) {
                $dataBugs["bugs"]["response"][] = array("message" => "El tipo de correspondencia de la fila " . $rowNumber . " no existe");
            }

            $camposFormularioVersion = $this->formularioVersion->getCampoFormulariosVersion();
            //Primero combino cada valor de la fila recuperada con su correspondiente tipoCampo
            //Para esto se recorre la cabecera del documento
            $dataToValidate = array_combine($headerWithoutFiles, $rowWithOutHeader);

            foreach ($dataToValidate as $key => $value) {
                foreach ($camposFormularioVersion as $campoFormularioVersion) {
                    //Validar cada campo según su naturaleza
                    if ($key == $campoFormularioVersion->getCampo()) {

                        $validIndice = true;
                        try {
                            $this->validateIndice($value, $campoFormularioVersion);
                        } catch (\Exception $e) {
                            $dataBugs["bugs"]["response"][] = array("message" => "Error por indice en la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . ":" . $e->getMessage());
                            $validIndice = false;
                        }

                        if($validIndice) {
                            $tipoCampo = $campoFormularioVersion->getTipoCampo();

                            switch ($tipoCampo) {
                                case "TextoCorto":
                                    if (null === $campoFormularioVersion->getLongitud()) {
                                        $longitud = 255;
                                    } else {
                                        $longitud = $campoFormularioVersion->getLongitud();
                                    }

                                    if (strlen($value) < $campoFormularioVersion->getValorMinimo()) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "La longitud del valor '" . $value . "' de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " es menor que el establecido (Longitud minima: " . $campoFormularioVersion->getValorMinimo() . ")");
                                    } else if (strlen($value) > $longitud) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "La longitud del valor '" . $value . "' de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " es mayor que el establecido (Longitud máxima: " . $longitud . ")");

                                    }
                                    break;
                                case "TextoLargo":
                                    break;
                                case "NumericoMoneda":
                                    if (!is_numeric($value)) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor númerico tipo moneda");
                                    }
                                    break;
                                case "NumericoDecimal":
                                    if (!is_numeric($value)) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor númerico decimal");
                                    }
                                    break;
                                case "NumericoEntero":
                                    if (!is_numeric($value)) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor númerico entero");
                                    }
                                    break;
                                case "Hora":
                                    if (!DateTimeUtils::validateDate($value, "H:i:s")) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es una hora valida (Formato válido: Horas:minutos:segundos)");
                                    }
                                    break;
                                case "Fecha":
                                    if (!DateTimeUtils::validateDate($value, "Y-m-d")) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es una fecha valida (Formato válido: AñoCompleto-mes-día)");
                                    }
                                    break;
                                case "Entidad":
                                    $queryBuilder = $this->em->createQueryBuilder();
                                    $entidad = $queryBuilder
                                        ->select("e.id")
                                        ->from('App\\Entity\\' . $campoFormularioVersion->getEntidad()->getDescripcion(), 'e')
                                        ->where('e.id = :id')
                                        ->setParameter("id", $value)
                                        ->getQuery()
                                        ->execute();
                                    if (count($entidad) == 0) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor relacionado a la entidad " . $campoFormularioVersion->getEntidad()->getDescripcion());
                                    }
                                    break;
                                case "FormularioVersion":
                                case "Formulario":
                                    $campoRelacionado = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioVersion->getCampoFormularioVersionId());
                                    $camposFormularioRelacionado = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array("campo_formulario_id" => $campoRelacionado->getCampoFormularioId()), array("id" => "DESC"));
                                    $registroFormulario = array();
                                    
                                    foreach ($camposFormularioRelacionado as $campoFormularioRelacionado) {
                                        $queryBuilder = $this->em->createQueryBuilder();
                                        $registroFormulario = $queryBuilder
                                        ->select("re")
                                        ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 're')
                                        ->where('re.id = :id')
                                        ->andWhere('re.campo_formulario_version_id = :campoFormularioIdRelacionado')
                                        ->setParameter("id", $value)
                                        ->setParameter("campoFormularioIdRelacionado", $campoFormularioRelacionado->getId())
                                        ->getQuery()
                                        ->execute();

                                        if (count($registroFormulario) > 0) {
                                            break;
                                        }
                                    }
                                    
                                    if (count($registroFormulario) == 0) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor relacionado al campo formularioVersion " . $campoFormularioRelacionado->getCampo() . " relacionado con el campo " . $campoFormularioVersion->getCampo());
                                    }
                                    break;
                                case "Multiseleccion":
                                    $values = explode("-", $value);
                                    foreach ($values as $listItem) {
                                        $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneBy(array("id" => $listItem, "lista_id" => $campoFormularioVersion->getListaId()));
                                        if (!isset($detalleLista)) {
                                            $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $listItem . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor relacionado a la lista Multiple " . $campoFormularioVersion->getLista()->getNombre());
                                        }
                                    }
                                    break;
                                case "Lista":
                                    $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneBy(array("id" => $value, "lista_id" => $campoFormularioVersion->getListaId()));
                                    if (!isset($detalleLista)) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormularioVersion->getCampo() . " no es un valor relacionado a la lista " . $campoFormularioVersion->getLista()->getNombre());
                                    }
                                    break;
                            }
                            break;
                        }
                    }
                }
            }
            $rowNumber++;
        }
        if (count($dataBugs) > 0) {
            return $dataBugs;
        }

    }

    private function processContent()
    {
        $usuario = $this->tokenStorage->getToken()->getUser();

        $header = $this->content[0];
        $headerWithoutFiles = $header;
        $lastColumn = end($header);
        if ($lastColumn == "Fecha Archivo") {
            array_pop($headerWithoutFiles);
            array_pop($headerWithoutFiles);
            array_pop($headerWithoutFiles);
        } else {
            array_pop($headerWithoutFiles);
        }
        // Tomar fila a fila e ir validando el contenido
        // Este contenido debe validarse segun la naturaleza del campo
        $rowNumber = 2;
        foreach ($this->contentWithOutHeader as $rowWithOutHeader) {
            //Guardar la cabecera del formularioVersion en la entidad Registro
            $registro = new Registro();
            $registro->setFormularioVersion($this->formularioVersion);
            $registro->setFechaHora(new DateTime());
            $registro->setEstadoId(1);
            $registro->setUsuario($usuario);
            $this->em->persist($registro);
            $lastColumn = end($header);
            if ($lastColumn == "Fecha Archivo") {
                $fechaArchivo = end($rowWithOutHeader);
                array_pop($rowWithOutHeader);
                $nombreArchivo = end($rowWithOutHeader);
                array_pop($rowWithOutHeader);
                $tipoCorrespondenciaId = end($rowWithOutHeader);
                array_pop($rowWithOutHeader);
            } else {
                $tipoCorrespondenciaId = end($rowWithOutHeader);
                array_pop($rowWithOutHeader);
            }
            $camposFormularioVersion = $this->formularioVersion->getCampoFormulariosVersion();
            //Primero combino cada valor de la fila recuperada con su correspondiente tipoCampo
            //Para esto se recorre la cabecera del documento
            $dataToValidate = array_combine($headerWithoutFiles, $rowWithOutHeader);

            foreach ($dataToValidate as $key => $value) {
                foreach ($camposFormularioVersion as $campoFormularioVersion) {
                    //Validar cada campo según su naturaleza
                    if ($key == $campoFormularioVersion->getCampo()) {
                        $tipoCampo = $campoFormularioVersion->getTipoCampo();
                        switch ($tipoCampo) {
                            case "Booleano":
                            case "TextoCorto":
                            case "TextoLargo":
                            case "NumericoMoneda":
                            case "NumericoDecimal":
                            case "NumericoEntero":
                                $this->savePrimitive($campoFormularioVersion->getTipoCampo(), $value, $registro, $campoFormularioVersion, 1);
                                break;
                            case "Hora":
                                $time = new \DateTime($value);
                                $this->saveDate($campoFormularioVersion->getTipoCampo(), $time, $registro, $campoFormularioVersion, 1);
                                break;
                            case "Fecha":
                                $date = new \DateTime($value);
                                $this->saveDate($campoFormularioVersion->getTipoCampo(), $date, $registro, $campoFormularioVersion, 1);
                                break;
                            case "Entidad":
                                $this->saveEntidad($registro, $campoFormularioVersion, $value, 1);
                                break;
                            case "FormularioVersion":
                            case "Formulario":
                                $this->saveCampo($registro, $campoFormularioVersion, $value, 1);
                                break;
                            case "Multiseleccion":
                                $valores = explode(", ", $value);
                                $descripcionValores = array();
                                foreach ($valores as $valor) {
                                    $descripcionValores[] = $this->saveLista($campoFormularioVersion->getTipoCampo(), $registro, $campoFormularioVersion, $valor, 1);
                                }
                                $this->buildResumen($campoFormularioVersion, implode(", ", $descripcionValores));
                                break;
                            case "Lista":
                                $descripcionValor = $this->saveLista($campoFormularioVersion->getTipoCampo(), $registro, $campoFormularioVersion, $value, 1);
                                break;
                        }
                        break;
                    }
                }
            }
            $rowNumber++;
            $registro->setResumen($this->mostrarFront);
            $registro->setBusqueda($this->busqueda);
            $sedeUsuario = null !== $usuario->getSedeId() ? $usuario->getSede()->getNombre() : $usuario->getProceso()->getSede()->getNombre();
            $registro->setSede($sedeUsuario);

            //Registrar el tipo de correspondencia
            //Cargar objeto tipo Correspondencias basado en el id de tipo_correspondencia recibido en la petición
            $tipoCorrespondencia = $this->em->getRepository(TipoCorrespondencia::class)
                ->findOneById($tipoCorrespondenciaId);
            //Validar si el año ha cambiado se debe reiniciar
            // el consecutivo de ese tipo y cambiar el año
            if (true === $tipoCorrespondencia->newYear()) {
                $tipoCorrespondencia->changeYear();
                $tipoCorrespondencia->setConsecutivo(1);
                $this->em->persist($tipoCorrespondencia);
                $this->em->flush();
            }

            //Registrar consecutivo
            $registro->setConsecutivo($tipoCorrespondencia->getConsecutivo());
            $registro->setTipoCorrespondencia($tipoCorrespondenciaId);
            $registro->setFormularioVersion($this->formularioVersion);
            $registro->setFechaFormularioVersion($this->formularioVersion->getFechaVersion());
            $registro->setNombreFormulario($this->formularioVersion->getNombre());
            $registro->setNomenclaturaFormulario($this->formularioVersion->getNomenclaturaFormulario());
            //Luego de un guardado exitoso del registro incrementar en 1 el consecutivo usado
            $tipoCorrespondencia->incrementarConsecutivo();
            $this->em->persist($tipoCorrespondencia);

            $this->em->flush();
            $radicado = $this->formularioVersion->getId() . '-' . $usuario->getSede()->getId() . '-' . $registro->getId();
            $registro->setRadicado($radicado);
            $this->em->flush();
            //Se valida que la ultia columna sea fecha archivo
            if ($lastColumn == "Fecha Archivo") {
                //obtener el archivo relacionado con el registro a subir
                $fileName = explode(".", $this->fileToProcess->getClientOriginalName());
                $fileNameWithOutExtension = $fileName[0];

                if (!file_exists($_ENV['TMP_LOCATION'] . "files_" . $fileNameWithOutExtension . ".zip")) {
                    return array("VerifyRelatedFilesInContent" => array("response" => array("0" => array("message" => "El archivo relacionado con estos datos no existe en el servidor"))));
                } else if (file_exists($_ENV['TMP_LOCATION'] . "files_" . $fileNameWithOutExtension . ".zip")) {
                    $fileToUpload = ZipManager::getZippedFile($_ENV['TMP_LOCATION'] . "files_" . $fileNameWithOutExtension . ".zip", $nombreArchivo);
                    file_put_contents($_ENV['TMP_LOCATION'] . $nombreArchivo, $fileToUpload);
                    // echo mime_content_type($_ENV['TMP_LOCATION'] . $nombreArchivo);
                    //die;
                    // Aca se extrae el directorio a almacenar a traves del valor enviado en estructura_formulario_id
                    $gestorArchivo = new GestorArchivos();
                    $rootFolder = $_ENV["FILE_LOCATION"];
                    $folder = date("Ymd");
                    $detalleNombreArchivo = explode(".", $nombreArchivo);
                    $nombreTipoDocumental = explode("(", $registro->getFormularioVersion()->getEstructuraDocumentalVersion()->getDescripcion());
                    $nuevoNombreArchivo = $_ENV['TMP_LOCATION'] . $fechaArchivo . "_" . $registro->getId() . "_" . TextUtils::slugifyWithUnderscore(trim($nombreTipoDocumental[0])) . "." . $detalleNombreArchivo[1];
                    rename($_ENV['TMP_LOCATION'] . $nombreArchivo, $nuevoNombreArchivo);
                    //Subir el archivo a Google Drive
                    $result = $gestorArchivo->uploadFile($this->em, $nuevoNombreArchivo, $folder, $rootFolder);
                    // se procede a guardar el registro en la BD
                    //Crear una entrada en la entidad archivo relacionando el nuevo archivo con el registro creado
                    $archivo = new Archivo();
                    // se setean todos los valores
                    $archivo->setRegistro($registro);
                    $archivo->setCarpeta($result["carpeta"]);
                    $archivo->setNombre($fechaArchivo . "_" . $registro->getId() . "_" . TextUtils::slugifyWithUnderscore(trim($nombreTipoDocumental[0])) . "." . $detalleNombreArchivo[1]);
                    $archivo->setIdentificador($result["gDriveFileSavedID"]);
                    $archivo->setVersion(1);
                    $archivo->setFechaVersion(new \DateTime());
                    $archivo->setComentario("Carga masiva");
                    $archivo->setEstadoId(1);
                    $archivo->setTipoDocumental(1);
                    $this->em->persist($archivo);
                    $this->em->flush();
                }
            }
            //buscar el nombre del tipo documental y concatenarlo para darle nombre al nuevo archivo

        }
        return array("VerifyRelatedFilesInContent" => array("response" => array("0" => array("message" => "Registros procesados!", "state" => "Ok"))));

    }

    protected function validateIndice($valor, $campoFormularioVersion) {
        if($campoFormularioVersion->getIndice() == true) {
            $camposFormularioVersion[] = $campoFormularioVersion;
            $campoFormularioId = $campoFormularioVersion->getId();
            $valorCuadroTexto = $campoFormularioVersion->getValorCuadroTexto();

            $value = $valor;

            if($campoFormularioVersion->getTipoCampo() == "Entidad") {
                $value = $this->getValueEntidad($campoFormularioVersion->getEntidadId(), $value, $campoFormularioVersion);
            }
            
            $result = $this->em->getRepository(Registro::class)->findIndexValue($this->em, $value, $campoFormularioId, $valorCuadroTexto);

            if(count($result) > 0) {
                throw new FormException(Response::HTTP_PRECONDITION_FAILED, "Ya existe un registro con el valor ". $result[0]["valor"] . ", este campo no permite otros registros con el mismo valor");
            }
        }
    }

    protected function getValueEntidad($entidadId, $entidadSelectedId, $campoFormularioVersion)
    {
        $entidad = $this->em->getRepository(Entidad::class)
        ->findOneById($entidadId);

        //$registroEntidad = $this->em->getRepository("\\App\\Entity\\" . $entidad->getNombre())->findOneById($entidadSelectedId);

        //$get = "get" . ucwords($columnName);
        
        
        //$valor = $registroEntidad->$get();
        
        $resultado = $this->em->getRepository("\\App\\Entity\\" . $entidad->getNombre())->findOneById($entidadSelectedId);

        if($campoFormularioVersion->getEntidadColumnName() == null) {
            $camposVisualizarEntidad = $entidad->getCampoVisualizar();
            $campos = explode("+", str_replace("-", "", $camposVisualizarEntidad));
        } else {
            $campos[] = $campoFormularioVersion->getEntidadColumnName();
        }
        
        foreach ($campos as $detalleCampo) {
            $get = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $detalleCampo)));
            if(isset($resultado)) {
                $detalleValor[] = $resultado->$get();
            } else {
                $detalleValor[] = "";
            }
        }
        $this->buildResumen($campoFormularioVersion, implode(" ", $detalleValor));

        $valor = implode(" ", $detalleValor);

        return $valor;
    }


    protected function saveDate($clase, $valor, $registroFormulario, $campoFormularioVersion, $estado_id)
    {
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setValor($valor);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        if ($clase == "Fecha") {
            $this->buildResumen($campoFormularioVersion, $valor->format("Y-m-d"));
        }
        if ($clase == "Hora") {
            $this->buildResumen($campoFormularioVersion, $valor->format("H:i"));
        }

    }

    protected function savePrimitive($clase, $valor, $registroFormulario, $campoFormularioVersion, $estado_id)
    {
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setValor(trim($valor));
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, trim($valor));
    }

    protected function saveEntidad($registroFormulario, $campoFormularioVersion, $id_entidad, $estado_id)
    { //Guardar en resumen el valor del campo Entidad relacionado
        $campo = $campoFormularioVersion->getEntidad()->getNombre();
        $camposVisualizarEntidad = $campoFormularioVersion->getEntidad()->getCampoVisualizar();

        $manager = $this->em->getRepository("\\App\\Entity\\" . $campo);
        $resultado = $manager->findOneBy(array("id" => $id_entidad));

        if($campoFormularioVersion->getEntidadColumnName() == null) {
            $camposVisualizarEntidad = $campoFormularioVersion->getEntidad()->getCampoVisualizar();
            $campos = explode("+", str_replace("-", "", $camposVisualizarEntidad));
        } else {
            $campos[] = $campoFormularioVersion->getEntidadColumnName();
        }

        foreach ($campos as $detalleCampo) {
            $get = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $detalleCampo)));
            if(isset($resultado)) {
                $detalleValor[] = $resultado->$get();
            } else {
                $detalleValor[] = "";
            }
        }

        $valor = implode(" ", $detalleValor);


        $entidad = new RegistroEntidad();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setIdEntidad($id_entidad);
        $entidad->setEstadoId($estado_id);
        $entidad->setValor($valor);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, $valor);
    }

    protected function saveOpcion($clase, $registroFormulario, $campoFormularioVersion, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById(trim($valor));
        $nombreClase = "App\Entity\RegistroLista";
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setDetalleLista($detalleLista);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);

        $this->buildResumen($campoFormularioVersion, $detalleLista->getDescripcion());
    }

    protected function saveLista($clase, $registroFormulario, $campoFormularioVersion, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById(trim($valor));
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setDetalleLista($detalleLista);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, trim($detalleLista->getDescripcion()));
        return $detalleLista->getDescripcion();
    }

    protected function saveCampo($registroFormulario, $campoFormularioVersion, $id_campo, $estado_id)
    {
        //Consultar Tipo de Campo relacionado con el campo del formularioVersion
        $campoFormularioOrigen = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioVersion->getCampoFormularioVersionId());

        //RegistroPadre
        $campoFormularioOrigen = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioVersion->getCampoFormularioVersionId());
        $camposFormularioRelacionado = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array("campo_formulario_id" => $campoFormularioOrigen->getCampoFormularioId()), array("id" => "DESC"));
        $registroPadre = array();
        
        foreach ($camposFormularioRelacionado as $campoFormularioOrigen) {
            $registroPadre = $this->em->createQueryBuilder()
            ->select("r.registro_id")
            ->from('App\\Entity\\Registro' . $campoFormularioOrigen->getTipoCampo(), 'r')
            ->where("r.campo_formulario_version_id = :campo_formulario_id")
            ->andWhere("r.id = :id")
            ->setParameter('campo_formulario_id', $campoFormularioOrigen->getId())
            ->setParameter('id', $id_campo)
            ->getQuery()
            ->execute();

            if (count($registroPadre) > 0) {
                break;
            }
        }
        
        //Guardar valores nuevos en entidad RegistroCampo
        //Consulto el valor basado en tu tipo_campo
        $queryBuilder = $this->em->createQueryBuilder();
        $result = $queryBuilder
            ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
            ->from('App\\Entity\\Registro' . $campoFormularioOrigen->getTipoCampo(), 'r')
            ->where("r.campo_formulario_version_id = :campo_formulario_id")
            ->andWhere("r.estado_id = :estado_id")
            ->andWhere("r.registro_id = :registro_id")
            ->setParameter('campo_formulario_id', $campoFormularioOrigen->getId())
            ->setParameter('estado_id', 1)
            ->setParameter('registro_id', $registroPadre[0]["registro_id"])
            ->getQuery()
            ->execute();

//extraer id_campo y registro_origen_id de la variable id
        $ids = explode("-", $result[0]["id"]);
        $id_campo = $ids[0];
        $registro_origen_id = $ids[1];
//Guardar valores nuevos en entidad RegistroCampo
        $entidad = new RegistroCampo();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setIdCampo($id_campo);
        $entidad->setRegistroIdOrigen($registro_origen_id);
        $entidad->setValor($result[0]["valor"]);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, $result[0]["valor"]);
    }

    protected function buildResumen($campoFormularioVersion, $valor)
    {
        if ($campoFormularioVersion->getMostrarFront() === true) {
            $this->mostrarFront[$campoFormularioVersion->getValorCuadroTexto()] = strval(trim($valor));
            $this->busqueda[$campoFormularioVersion->getCampo()] = strval(trim($valor));
        }
    }

    private function ReadFileContent()
    {
        $this->fileToProcess->move($_ENV['TMP_LOCATION'], $this->fileToProcess->getClientOriginalName());
        $streamFileToProcess = fopen($_ENV['TMP_LOCATION'] . $this->fileToProcess->getClientOriginalName(), "r");
        $fila = 1;
        while (($datos = fgetcsv($streamFileToProcess, 4000, ",")) !== false) {
            $this->content[] = $datos;
        }
        fclose($streamFileToProcess);
    }

    private function VerifyRelatedFilesInContent()
    {
        $header = $this->content[0];
        //Quito la primera fila del arreglo
        $this->contentWithOutHeader = $this->content;
        array_shift($this->contentWithOutHeader);
        ///verificar si la palabra Archivos esta al final de este arreglo
        if (end($header) == "Fecha Archivo") {
            // Verificar que exista un archivo .zip con el
            // nombre relacionado en el nombre del archivo subido.
            $fileName = explode(".", $this->fileToProcess->getClientOriginalName());
            //historia-laboral_cperez_20190608105944.csv
            $fileNameWithOutExtension = $fileName[0];
            if (!file_exists($_ENV['TMP_LOCATION'] . "files_" . $fileNameWithOutExtension . ".zip")) {
                return array("VerifyRelatedFilesInContent" => array("response" => array("0" => array("message" => "El archivo relacionado con estos datos no existe en el servidor"))));
            } else if (file_exists($_ENV['TMP_LOCATION'] . "files_" . $fileNameWithOutExtension . ".zip")) {
                $zipContent = ZipManager::ReadContent($_ENV['TMP_LOCATION'] . "files_" . $fileNameWithOutExtension . ".zip");
                //Valida que cada uno de los archivos relacionados si esten en el $content obtenido
                $rowNumber = 2;
                foreach ($this->contentWithOutHeader as $row) {
                    array_pop($row);
                    $fileNameToValidate = end($row);
                    if (!in_array($fileNameToValidate, $zipContent)) {
                        return array("VerifyRelatedFilesInContent" => array("response" => array("0" => array("message" => "El archivo " . $fileNameToValidate . " ubicado en la fila " . $rowNumber . " no se encuentra en el archivo files_" . $fileNameWithOutExtension . ".zip"))));
                    }
                    $rowNumber++;
                }
            }
        }
    }
}
