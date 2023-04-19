<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\CampoFormulario;
use App\Entity\DetalleLista;
use App\Entity\Formulario;
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

/**
 * Undocumented class
 */
class ProcesarEstructuraModeloService
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
            //Validar que la cantidad de elementos sea igual a la cantidad de columnas del formulario
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
        // Recibir el identificador del formulario relacionado
        $formulario_id = $request->attributes->get("id");
        //Validamos que el identificador del formulario nos devuelva un obejto de este tipo
        $this->formulario = $this->em->getRepository(Formulario::class)->findOneById($formulario_id);
        if (!isset($this->formulario)) {
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
        $camposFormulario = $this->formulario->getCampoFormularios();
        foreach ($camposFormulario as $campoFormulario) {
            if ($campoFormulario->getEstadoId() == 1 && $campoFormulario->getOcultoAlRadicar() != 1) {
                $formHeader[] = $campoFormulario->getCampo();
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
            return array("validateColumns" => array("response" => array("0" => array("message" => "La cabecera del formulario no es igual que el archivo enviado"))));
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

            $camposFormulario = $this->formulario->getCampoFormularios();
            //Primero combino cada valor de la fila recuperada con su correspondiente tipoCampo
            //Para esto se recorre la cabecera del documento
            $dataToValidate = array_combine($headerWithoutFiles, $rowWithOutHeader);
            foreach ($dataToValidate as $key => $value) {
                foreach ($camposFormulario as $campoFormulario) {
                    //Validar cada campo según su naturaleza
                    if ($key == $campoFormulario->getCampo()) {
                        $tipoCampo = $campoFormulario->getTipoCampo();
                        switch ($tipoCampo) {
                            case "TextoCorto":
                                if (null === $campoFormulario->getLongitud()) {
                                    $longitud = 255;
                                } else {
                                    $longitud = $campoFormulario->getLongitud();
                                }

                                if (strlen($value) < $campoFormulario->getValorMinimo()) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "La longitud del valor '" . $value . "' de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " es menor que el establecido (Longitud minima: " . $campoFormulario->getValorMinimo() . ")");
                                } else if (strlen($value) > $longitud) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "La longitud del valor '" . $value . "' de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " es mayor que el establecido (Longitud máxima: " . $longitud . ")");

                                }
                                break;
                            case "TextoLargo":
                                break;
                            case "NumericoMoneda":
                                if (!is_numeric($value)) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor númerico tipo moneda");
                                }
                                break;
                            case "NumericoDecimal":
                                if (!is_numeric($value)) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor númerico decimal");
                                }
                                break;
                            case "NumericoEntero":
                                if (!is_numeric($value)) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor númerico entero");
                                }
                                break;
                            case "Hora":
                                if (!DateTimeUtils::validateDate($value, "H:i:s")) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es una hora valida (Formato válido: Horas:minutos:segundos)");
                                }
                                break;
                            case "Fecha":
                                if (!DateTimeUtils::validateDate($value, "Y-m-d")) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es una fecha valida (Formato válido: AñoCompleto-mes-día)");
                                }
                                break;
                            case "Entidad":
                                $queryBuilder = $this->em->createQueryBuilder();
                                $entidad = $queryBuilder
                                    ->select("e.id")
                                    ->from('App\\Entity\\' . $campoFormulario->getEntidad()->getDescripcion(), 'e')
                                    ->where('e.id = :id')
                                    ->setParameter("id", $value)
                                    ->getQuery()
                                    ->execute();
                                if (count($entidad) == 0) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor relacionado a la entidad " . $campoFormulario->getEntidad()->getDescripcion());
                                }
                                break;
                            case "Formulario":
                                //Consultar el campo CampoFormularioId relacionado con este campo
                                $campoFormularioRelacionado = $this->em->getRepository(CampoFormulario::class)->findOneById($campoFormulario->getCampoFormularioId());
                                //Consulto los valores relacionados con este campo relacionado
                                $queryBuilder = $this->em->createQueryBuilder();
                                $registroFormulario = $queryBuilder
                                    ->select("re")
                                    ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 're')
                                    ->where('re.id = :id')
                                    ->andWhere('re.campo_formulario_id = :campoFormularioIdRelacionado')
                                    ->setParameter("id", $value)
                                    ->setParameter("campoFormularioIdRelacionado", $campoFormularioRelacionado->getId())
                                    ->getQuery()
                                    ->execute();
                                if (count($registroFormulario) == 0) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor relacionado al campo formulario " . $campoFormularioRelacionado->getCampo() . " relacionado con el campo " . $campoFormulario->getCampo());
                                }
                                break;
                            case "Multiseleccion":
                                $values = explode("-", $value);
                                foreach ($values as $listItem) {
                                    $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneBy(array("id" => $listItem, "lista_id" => $campoFormulario->getListaId()));
                                    if (!isset($detalleLista)) {
                                        $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $listItem . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor relacionado a la lista Multiple " . $campoFormulario->getLista()->getNombre());
                                    }
                                }
                                break;
                            case "Lista":
                                $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneBy(array("id" => $value, "lista_id" => $campoFormulario->getListaId()));
                                if (!isset($detalleLista)) {
                                    $dataBugs["bugs"]["response"][] = array("message" => "El valor " . $value . " de la fila " . $rowNumber . ", columna " . $campoFormulario->getCampo() . " no es un valor relacionado a la lista " . $campoFormulario->getLista()->getNombre());
                                }
                                break;
                        }
                        break;
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
            //Guardar la cabecera del formulario en la entidad Registro
            $registro = new Registro();
            $registro->setFormularioVersion($this->formulario);
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
            $camposFormulario = $this->formulario->getCampoFormularios();
            //Primero combino cada valor de la fila recuperada con su correspondiente tipoCampo
            //Para esto se recorre la cabecera del documento
            $dataToValidate = array_combine($headerWithoutFiles, $rowWithOutHeader);
            foreach ($dataToValidate as $key => $value) {
                foreach ($camposFormulario as $campoFormulario) {
                    //Validar cada campo según su naturaleza
                    if ($key == $campoFormulario->getCampo()) {
                        $tipoCampo = $campoFormulario->getTipoCampo();
                        switch ($tipoCampo) {
                            case "Booleano":
                            case "TextoCorto":
                            case "TextoLargo":
                            case "NumericoMoneda":
                            case "NumericoDecimal":
                            case "NumericoEntero":
                                $this->savePrimitive($campoFormulario->getTipoCampo(), $value, $registro, $campoFormulario, 1);
                                break;
                            case "Hora":
                                $time = new \DateTime($value);
                                $this->saveDate($campoFormulario->getTipoCampo(), $time, $registro, $campoFormulario, 1);
                                break;
                            case "Fecha":
                                $date = new \DateTime($value);
                                $this->saveDate($campoFormulario->getTipoCampo(), $date, $registro, $campoFormulario, 1);
                                break;
                            case "Entidad":
                                $this->saveEntidad($registro, $campoFormulario, $value, 1);
                                break;
                            case "Formulario":
                                $this->saveCampo($registro, $campoFormulario, $value, 1);
                                break;
                            case "Multiseleccion":
                                $valores = $value;
                                $descripcionValores = array();
                                foreach ($valores as $valor) {
                                    $descripcionValores[] = $this->saveLista($campoFormulario->getTipoCampo(), $registro, $campoFormulario, $valor->{"id"}, 1);
                                }
                                $this->buildResumen($campoFormulario, implode(", ", $descripcionValores));
                                break;
                            case "Lista":
                                $descripcionValor = $this->saveLista($campoFormulario->getTipoCampo(), $registro, $campoFormulario, $value, 1);
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
            $registro->setFormularioVersion($this->formulario->getVersion());
            $registro->setFechaFormularioVersion($this->formulario->getFechaVersion());
            $registro->setNombreFormulario($this->formulario->getNombre());
            $registro->setNomenclaturaFormulario($this->formulario->getNomenclaturaFormulario());
            //Luego de un guardado exitoso del registro incrementar en 1 el consecutivo usado
            $tipoCorrespondencia->incrementarConsecutivo();
            $this->em->persist($tipoCorrespondencia);

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
                    $nombreTipoDocumental = explode("(", $registro->getFormulario()->getTablaRetencion()->getEstructuraDocumental()->getDescripcion());
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

    protected function saveDate($clase, $valor, $registroFormulario, $campoFormulario, $estado_id)
    {
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormulario($campoFormulario);
        $entidad->setValor($valor);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        if ($clase == "Fecha") {
            $this->buildResumen($campoFormulario, $valor->format("Y-m-d"));
        }
        if ($clase == "Hora") {
            $this->buildResumen($campoFormulario, $valor->format("H:i"));
        }

    }

    protected function savePrimitive($clase, $valor, $registroFormulario, $campoFormulario, $estado_id)
    {
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormulario($campoFormulario);
        $entidad->setValor(trim($valor));
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormulario, trim($valor));
    }

    protected function saveEntidad($registroFormulario, $campoFormulario, $id_entidad, $estado_id)
    { //Guardar en resumen el valor del campo Entidad relacionado
        $campo = $campoFormulario->getEntidad()->getNombre();
        $camposVisualizarEntidad = $campoFormulario->getEntidad()->getCampoVisualizar();
        switch ($campo) {
            case "Ciudad":
                $manager = $this->em->getRepository(\App\Entity\Ciudad::class);
                break;
            case "Contacto":
                $manager = $this->em->getRepository(\App\Entity\Contacto::class);
                break;
            case "Tercero":
                $manager = $this->em->getRepository(\App\Entity\Tercero::class);
                break;
            case "Cargo":
                $manager = $this->em->getRepository(\App\Entity\Cargo::class);
                break;
            case "Proceso":
                $manager = $this->em->getRepository(\App\Entity\Proceso::class);
                break;
            case "Rol":
                $manager = $this->em->getRepository(\App\Entity\Rol::class);
                break;
            case "Usuario":
                $manager = $this->em->getRepository(\App\Entity\Usuario::class);
                break;
            case "Sede":
                $manager = $this->em->getRepository(\App\Entity\Sede::class);
                break;
        }
        $resultado = $manager->findOneBy(array("id" => $id_entidad));
        $campos = explode("+", str_replace("-", "", $camposVisualizarEntidad));
        foreach ($campos as $detalleCampo) {
            $get = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $detalleCampo)));
            $detalleValor[] = trim($resultado->$get());
        }
        $valor = implode(" ", $detalleValor);
        $entidad = new RegistroEntidad();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormulario);
        $entidad->setIdEntidad($id_entidad);
        $entidad->setEstadoId($estado_id);
        $entidad->setValor($valor);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormulario, $valor);
    }

    protected function saveOpcion($clase, $registroFormulario, $campoFormulario, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById(trim($valor));
        $nombreClase = "App\Entity\RegistroLista";
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormulario($campoFormulario);
        $entidad->setDetalleLista($detalleLista);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);

        $this->buildResumen($campoFormulario, $detalleLista->getDescripcion());
    }

    protected function saveLista($clase, $registroFormulario, $campoFormulario, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById(trim($valor));
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = new $nombreClase();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormulario($campoFormulario);
        $entidad->setDetalleLista($detalleLista);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        return $detalleLista->getDescripcion();
        $this->buildResumen($campoFormulario, trim($valor));
    }

    protected function saveCampo($registroFormulario, $campoFormulario, $id_campo, $estado_id)
    {
        //Consultar Tipo de Campo relacionado con el campo del formulario
        $campoFormularioOrigenId = $campoFormulario->getCampoFormularioId();
        $campoFormularioOrigen = $this->em->getRepository(CampoFormulario::class)->findOneById($campoFormularioOrigenId);

        //RegistroPadre
        $queryBuilderRegistroPadre = $this->em->createQueryBuilder();

        $registroPadre = $queryBuilderRegistroPadre
            ->select("r.registro_id")
            ->from('App\\Entity\\Registro' . $campoFormularioOrigen->getTipoCampo(), 'r')
            ->where("r.campo_formulario_id = :campo_formulario_id")
            ->andWhere("r.id = :id")
            ->setParameter('campo_formulario_id', $campoFormularioOrigen->getId())
            ->setParameter('id', $id_campo)
            ->getQuery()
            ->execute();
        //Guardar valores nuevos en entidad RegistroCampo
        //Consulto el valor basado en tu tipo_campo
        $queryBuilder = $this->em->createQueryBuilder();
        $result = $queryBuilder
            ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
            ->from('App\\Entity\\Registro' . $campoFormularioOrigen->getTipoCampo(), 'r')
            ->where("r.campo_formulario_id = :campo_formulario_id")
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
        $entidad->setCampoFormularioVersion($campoFormulario);
        $entidad->setIdCampo($id_campo);
        $entidad->setRegistroIdOrigen($registro_origen_id);
        $entidad->setValor($result[0]["valor"]);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormulario, $result[0]["valor"]);
    }

    protected function buildResumen($campoFormulario, $valor)
    {
        if ($campoFormulario->getMostrarFront() === true) {
            $this->mostrarFront[$campoFormulario->getValorCuadroTexto()] = strval(trim($valor));
            $this->busqueda[$campoFormulario->getCampo()] = strval(trim($valor));
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
