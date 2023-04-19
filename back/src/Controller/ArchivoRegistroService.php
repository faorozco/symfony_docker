<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\CampoFormularioVersion;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use App\Entity\RegistroEntidad;
use App\Entity\RegistroCampo;
use App\Entity\TipoCorrespondencia;
use App\Entity\DetalleLista;
use App\Utils\Auditor;
use App\Utils\Gdrive;
use App\Utils\GestorArchivos;
use App\Utils\TextUtils;
use App\Entity\EjecucionPaso;
use App\Utils\StickerGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class ArchivoRegistroService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->ejecucion_paso_id = '';
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function save(Request $request)
    {

        //validar tamaño máximo de subida de archivo en el servidor.
        // estructura_formulario_id
        // registro_id
        // version
        // comentario
        // archivo a guardar
        //$data = json_decode($request->getContent());
        $fileToUpload = $request->files->get("archivo");
        //recibir los parametros de la petición:
        $registro = $this->em->getRepository(Registro::class)->findOneById($request->attributes->get("id"));
        $version = $request->request->get("version");
        $comentario = $request->request->get("comentario");
        $tipo_archivo = $request->request->get("tipo_archivo");
        $fecha_vigencia = $request->request->get("fecha_vigencia");
        $archivo_origen = $request->request->get("arhivo_origen");
        $this->ejecucion_paso_id = $request->request->get("ejecucion_paso_id");
        

        $esTipoDocumental = $request->request->get("es_tipo_documental");
        $MensajetipoDocumental = "";
        // Cargar un objeto con el tipo de formularioVersion escogido como formularioVersion relacionado.
        $formularioRelacionado = $this->em->getRepository(FormularioVersion::class)->findOneById($request->request->get("formulariorelacionado"));
        $archivoRelacionado = $request->request->get("archivorelacionado");
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($registro->getFormularioVersionId());

        if (null !== $fileToUpload) {
            $nombreArchivo = $fileToUpload->getClientOriginalName();
        } else if (null !== $archivoRelacionado) {
            //leer el nombre del archivo de Google Drive basado esn su ID
            $client = new Gdrive();
            $clientGDocument = $client->getClient();
            $service = new \Google_Service_Drive($clientGDocument);
            $file = $client->readFile($service, $archivoRelacionado);
            $nombreArchivo = $file["fileMetada"]->name;
        }



        if (null !== $formularioRelacionado) {
            // Construcción automática de registros basados en el formularioVersion seleccionado
            //Consultar los campos de formularioVersion de tipo FormularioVersion
            $camposFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findBy(
                array(
                    "formulario_version_id" => $formularioRelacionado->getId(),
                    "tipo_campo" => "FormularioVersion",
                )
            );
            $registrosRelacionados = array();
            foreach ($camposFormularioVersion as $campoFormularioVersion) {
                //Validar cual de estos campos tiene relación en registroCampo
                //Consultar si ya existe un formularioVersion relacionado a este tipo de formularioVersion relacionado a este registro
                $result = $this->em->getRepository(RegistroCampo::class)->findBy(
                    array(
                        "campo_formulario_version_id" => $campoFormularioVersion->getId(),
                        "registro_id_origen" => $request->attributes->get("id"),
                    )
                );
                if (count($result) > 0) {
                    $registrosRelacionados[] = $result;
                }
            }
            if (count($registrosRelacionados) > 0) {
                foreach ($registrosRelacionados as $registroRelacionado) {
                    $registro = $this->em->getRepository(Registro::class)->findOneById($registroRelacionado[0]->getRegistro()->getId());
                }
            } else {
                //Si existe cargarlo
                //Mirar si ya existe un registro creado para ese tipo de FormularioVersion
                //Si no crearlo
                $registro = $this->saveRegistro($registro, $formularioVersion, $formularioRelacionado);
            }
        }

        if ($esTipoDocumental == 'S') {
            //verificar si se asigno un archivo relacionado

            $tipoDocumental = true;
            $MensajetipoDocumental = "TIPO DOCUMENTAL";
            $fecha = $request->request->get("fecha_documento");

            if (null !== $archivoRelacionado) {
                //leer el nombre del archivo de Google Drive basado esn su ID
                $nombreArray = explode('.', $nombreArchivo);
            } else if (null === $archivoRelacionado) {
                $nombreArray = explode('.', $fileToUpload->getClientOriginalName());
            }

            $extension = end($nombreArray);

            $nombreEstructuraDocumental = $registro->getFormularioVersion()->getEstructuraDocumentalVersion()->getDescripcion();

            $nombreGuionBajo = TextUtils::slugifyWithUnderscore($nombreEstructuraDocumental);

            $nombreArchivo = $nombreEstructuraDocumental . "." . $extension;

            if (isset($fecha) && strlen($fecha) > 0) {
                $nombreArchivo = $fecha . '-' . $nombreArchivo;
            }
        } else {
            $tipoDocumental = false;
        }

        $usuario = $this->tokenStorage->getToken()->getUser();

        // Se crea un objeto archivo con estructura_formulario_id, registro_id y version.
        // Si este objeto retorna true es porque ya existe un archivo con esa misma versión
        // por tanto no se puede dejar almacena
        if (null === $registro) {
            return array("response" => "No existe un registro asociado al identificador enviado");
        } else {
            /*$archivoExistente = $this->em->getRepository(Archivo::class)->findOneBy(
                array(
                    "registro_id" => $registro->getId(),
                    // "version" => $version,
                    "nombre" => $nombreArchivo,
                ),
                array('id' => 'DESC')
            );*/

            //Validar versión del archivo

            //Si el archivo es nuevo empieza la versión en 1
            //Si ya existe se calcula la versión con el existente y se incrementa 1
            //Registro Auditoria

            if (null === $archivoRelacionado) {
                $fileUploaded = $this->saveFile($registro, $fileToUpload, $comentario, 1, $nombreArchivo, $tipoDocumental, $tipo_archivo, $fecha_vigencia, $archivo_origen);
            } else if (null !== $archivoRelacionado) {
                $fileUploaded = $this->saveRemoteFile($registro, $archivoRelacionado, $comentario, 1, $nombreArchivo, $tipoDocumental, $tipo_archivo, $fecha_vigencia, $archivo_origen);
            }

            $accion = "NUEVO ARCHIVO";

            if (isset($MensajetipoDocumental) && strlen($MensajetipoDocumental) > 0) {
                $accion = $accion . " " . $MensajetipoDocumental;
            }

            $valor_actual = array(
                "Radicado" => $registro->getId(),
                "Archivo" => $nombreArchivo,
                "Versión" => "1",
                "Tipo archivo" => $tipo_archivo,
                "Fecha vigencia" => $fecha_vigencia,
                "Archivo origen" => $fileUploaded->getArchivoOrigen()
            );

            Auditor::registerAction($this->em, $registro, $usuario, null, $valor_actual, $accion);
            return $fileUploaded;


            /*else {
                //Calcular la nueva versión del documento
                $newVersion = $archivoExistente->getVersion() + 1;
                $archivoExistente->setEstadoId(0);
                $this->em->persist($archivoExistente);
                $this->em->flush();

                //Registro Auditoria
                $valor_actual = array(
                    "Radicado" => $registro->getId(),
                    "Archivo" => $nombreArchivo,
                    "Versión" => $newVersion,
                );
                if (null === $archivoRelacionado) {
                    $fileUploaded = $this->saveFile($registro, $fileToUpload, $comentario, $newVersion, $nombreArchivo, $tipoDocumental);
                } else if (null !== $archivoRelacionado) {
                    $fileUploaded = $this->saveRemoteFile($registro, $archivoRelacionado, $comentario, $newVersion, $nombreArchivo, $tipoDocumental);
                }
                Auditor::registerAction($this->em, $registro, $usuario, null, $valor_actual, "ACTUALIZAR ARCHIVO " . $MensajetipoDocumental);
                return $fileUploaded;
            }*/
        }
    }

    protected function saveFile($registro, $fileToUpload, $comentario, $version = 1, $nombreArchivo, $tipoDocumental = false, $tipo_archivo, $fecha_vigencia, $archivo_origen)
    {
        // Si devuelve false se guarda el archivo enviado en Google Drive.
        // se genera el valor para el atributo fecha_version
        $gestorArchivo = new GestorArchivos();
        // Aca se extrae el directorio a almacenar a traves del valor enviado en estructura_formulario_id
        $rootFolder = $_ENV["FILE_LOCATION"];
        $folder = date("Ymd");
        $result = $gestorArchivo->uploadFile($this->em, $fileToUpload, $folder, $rootFolder);
        // se procede a guardar el registro en la BD
        //se Crea un objeto de tipo Archivo
        $archivo = new Archivo();
        // se setean todos los valores
        $archivo->setRegistro($registro);
        $archivo->setVersion($version);
        $archivo->setFechaVersion(new \DateTime());
        $archivo->setComentario($comentario == 'null' ? "" : $comentario);
        $archivo->setEstadoId(1);
        $archivo->setNombre($nombreArchivo);
        $archivo->setTipoDocumental($tipoDocumental);
        $archivo->setIdentificador($result["gDriveFileSavedID"]);
        $archivo->setCarpeta($result["carpeta"]);
        $archivo->setTipoArchivo($tipo_archivo);
        if(isset($this->ejecucion_paso_id)){
            $saveFileEjecucion = $this->em->getRepository(EjecucionPaso::class)->findOneById($this->ejecucion_paso_id);
            $saveFileEjecucion->setFile('1');
            $this->em->persist($saveFileEjecucion);
            $archivo->setEjecucionPasoId($this->ejecucion_paso_id);
        }

        if ($tipo_archivo == 'vigencia') {
            $archivo->setFechaVigencia(\DateTime::createFromFormat('j/m/Y', $fecha_vigencia));
        }

        $archivo->setArchivoOrigen($archivo_origen);
        $archivo->setVigente(1);

        $this->em->persist($archivo);
        $this->em->flush();

        if ($version == 1) {
            $archivo->setArchivoOrigen($archivo->getId());
            $this->em->persist($archivo);
            $this->em->flush();
        }

        return $archivo;
    }

    protected function saveRemoteFile($registro, $remoteFile, $comentario, $version = 1, $nombreArchivo, $tipoDocumental = false, $tipo_archivo, $fecha_vigencia, $archivo_origen)
    {
        // Si devuelve false se guarda el archivo enviado en Google Drive.
        // se genera el valor para el atributo fecha_version
        $gestorArchivo = new GestorArchivos();
        // Aca se extrae el directorio a almacenar a traves del valor enviado en estructura_formulario_id
        $rootFolder = $_ENV["FILE_LOCATION"];
        $folder = date("Ymd");
        $result = $gestorArchivo->uploadRemoteFile($this->em, $remoteFile, $folder, $rootFolder);
        // se procede a guardar el registro en la BD
        //se Crea un objeto de tipo Archivo
        $archivo = new Archivo();
        // se setean todos los valores
        $archivo->setRegistro($registro);
        $archivo->setVersion($version);
        $archivo->setFechaVersion(new \DateTime());
        $archivo->setComentario($comentario == 'null' ? "" : $comentario);
        $archivo->setEstadoId(1);
        $archivo->setNombre($nombreArchivo);
        $archivo->setTipoDocumental($tipoDocumental);
        $archivo->setIdentificador($result["gDriveFileSavedID"]);
        $archivo->setCarpeta($result["carpeta"]);
        $archivo->setTipoArchivo($tipo_archivo);
        if(isset($this->ejecucion_paso_id)){
            $saveFileEjecucion = $this->em->getRepository(EjecucionPaso::class)->findOneById($this->ejecucion_paso_id);
            $saveFileEjecucion->setFile('1');
            $this->em->persist($saveFileEjecucion);
            $archivo->setEjecucionPasoId($this->ejecucion_paso_id);
        }

        if ($tipo_archivo == 'vigencia') {
            $archivo->setFechaVigencia(new \DateTime($fecha_vigencia));
        }

        $archivo->setArchivoOrigen($archivo_origen);
        $archivo->setVigente(1);

        $this->em->persist($archivo);
        $this->em->flush();

        if ($version == 1) {
            $archivo->setArchivoOrigen($archivo->getId());
            $this->em->persist($archivo);
            $this->em->flush();
        }
        return $archivo;
    }

    public function saveRegistro($registroPadre, $formularioVersion, $formularioRelacionado)
    {
        $camposFormularioRelacionado = $formularioRelacionado->getCampoFormulariosVersion();
        $registroFormulario = $registroPadre->getRegistroCampos();
        //Recordar cambiar campo fecha_hora de registro por un tipo datetime
        $fecha_hora = new \DateTime();
        //Primero capturo la petición json de guardado de un registro de un formularioVersion
        $registroFormularioRelacionado = new Registro();
        //cargo primero el objeto formularioVersion

        try {
            $mostrarFront = array();
            $busqueda = array();

            $usuario = $this->tokenStorage->getToken()->getUser();
            $registroFormularioRelacionado->setFormularioVersion($formularioRelacionado);
            $registroFormularioRelacionado->setFechaFormularioVersion($formularioRelacionado->getFechaVersion());
            $registroFormularioRelacionado->setNombreFormulario($formularioRelacionado->getNombre());
            $registroFormularioRelacionado->setNomenclaturaFormulario($formularioRelacionado->getNomenclaturaFormulario());
            $registroFormularioRelacionado->setFechaHora($fecha_hora);
            $registroFormularioRelacionado->setEstadoId(1);
            $registroFormularioRelacionado->setUsuario($usuario);
            //Si el usuario tiene una sede diferente a la que tiene registrada en el proceso
            // colocar la sede asignada por el usuario
            if ($usuario->getSede() != $usuario->getProceso()->getSede() && null !== $usuario->getSede()) {
                $sedeUsuario = $usuario->getSede();
            } else if ($usuario->getSede() == $usuario->getProceso()->getSede() || null === $usuario->getSede()) {
                $sedeUsuario = $usuario->getProceso()->getSede();
            }

            //Registrar el tipo de correspondencia
            //Cargar objeto tipo Correspondencias basado en el id de tipo_correspondencia recibido en la petición
            $tipoCorrespondencia = $this->em->getRepository(TipoCorrespondencia::class)
                ->findOneById(1);
            //Validar si el año ha cambiado se debe reiniciar
            // el consecutivo de ese tipo y cambiar el año
            if (true === $tipoCorrespondencia->newYear()) {
                $tipoCorrespondencia->changeYear();
                $tipoCorrespondencia->setConsecutivo(1);
                $this->em->persist($tipoCorrespondencia);
                $this->em->flush();
            }

            //Registrar consecutivo
            $registroFormularioRelacionado->setConsecutivo($tipoCorrespondencia->getConsecutivo());
            $registroFormularioRelacionado->setTipoCorrespondencia($tipoCorrespondencia->getId());
            $registroFormularioRelacionado->setSede($sedeUsuario->getNombre());

            $registroFormularioRelacionado->setResumen($mostrarFront);
            $registroFormularioRelacionado->setBusqueda($busqueda);
            $this->em->persist($registroFormularioRelacionado);
            $this->em->flush();

            $radicado = $formularioRelacionado->getId() . "-" . $sedeUsuario->getId() . "-" . $registroFormularioRelacionado->getId();
            $registroFormularioRelacionado->setRadicado($radicado);
            $this->em->persist($registroFormularioRelacionado);
            $this->em->flush();

            //Guardar valores nulos en los campos
            //El unico campo que lleva un valor es el campo tipo formularioVersion.
            foreach ($camposFormularioRelacionado as $campoFormularioVersion) {
                //Cargo el tipo de registro basado en campo_formulario_id
                //TODO: RegistroBoolean
                switch ($campoFormularioVersion->getTipoCampo()) {
                    case "Booleano":
                    case "TextoCorto":
                    case "TextoLargo":
                    case "NumericoMoneda":
                    case "NumericoDecimal":
                    case "NumericoEntero":

                        $defaultValue = "";
                        if (($campoFormularioVersion->getTipoCampo() == "NumericoMoneda"
                            || $campoFormularioVersion->getTipoCampo() == "NumericoDecimal"
                            || $campoFormularioVersion->getTipoCampo() == "NumericoEntero") && $defaultValue == "") {
                            $defaultValue = null;
                        }

                        $this->savePrimitive($campoFormularioVersion->getTipoCampo(), $defaultValue, $registroFormularioRelacionado, $campoFormularioVersion, 1);
                        break;
                    case "Hora":
                        $time = new \DateTime("00:00:00");
                        $this->saveDate($campoFormularioVersion->getTipoCampo(), $time, $registroFormularioRelacionado, $campoFormularioVersion, 1);
                        break;
                    case "Fecha":
                        $date = new \DateTime();
                        $this->saveDate($campoFormularioVersion->getTipoCampo(), $date, $registroFormularioRelacionado, $campoFormularioVersion, 1);
                        break;
                    case "Entidad":
                        $this->saveEntidad($registroFormularioRelacionado, $campoFormularioVersion, 1, 1);
                        break;
                    case "FormularioVersion":
                    case "Formulario":
                        //Consultar el valor del registro formularioVersion
                        $this->saveCampo($registroFormularioRelacionado, $campoFormularioVersion, $registroPadre, 1);
                        break;
                    case "Opcion":

                        $this->saveOpcion($campoFormularioVersion->getTipoCampo(), $registroFormularioRelacionado, $campoFormularioVersion, null, 1);
                        break;
                    case "Multiseleccion":
                        $valores = array(null);
                        $descripcionValores = array();
                        foreach ($valores as $valor) {
                            $descripcionValores[] = $this->saveLista($campoFormularioVersion->getTipoCampo(), $registroFormularioRelacionado, $campoFormularioVersion, $valor, 1);
                        }
                        //$this->buildResumen($campoFormularioVersion, implode(", ", $descripcionValores));
                        break;
                    case "Lista":

                        $descripcionValor = $this->saveLista($campoFormularioVersion->getTipoCampo(), $registroFormularioRelacionado, $campoFormularioVersion, null, 1);

                        //$this->buildResumen($campoFormularioVersion, $descripcionValor);
                        break;
                }
            }

            //Luego de un guardado exitoso del registro incrementar en 1 el consecutivo usado
            $tipoCorrespondencia->incrementarConsecutivo();
            $this->em->persist($tipoCorrespondencia);
            $registroFormularioRelacionado->setResumen($this->mostrarFront);
            $registroFormularioRelacionado->setBusqueda($this->busqueda);
            $this->em->persist($registroFormularioRelacionado);
            $this->em->flush();

            //Verificar si el formularioVersion tiene la opción radicado electrónico seleccionada.
            // Si es asi ejecutar la acción que genera el sticker de radicado electrónico
            self::crearRadicadoElectronico($registroFormularioRelacionado);
        } catch (\Exception $e) {
            $message = array("return" => array("response" => $e->getMessage()));
            return $message;
        }
        return $registroFormularioRelacionado;
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
        $entidad->setValor($valor);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, $valor);
    }

    protected function saveEntidad($registroFormulario, $campoFormularioVersion, $id_entidad, $estado_id)
    {
        $entidad = new RegistroEntidad();
        $entidad->setRegistro($registroFormulario);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setIdEntidad($id_entidad);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);

        //Guardar en resumen el valor del campo Entidad relacionado
        $campo = $campoFormularioVersion->getEntidad()->getNombre();
        $camposVisualizarEntidad = $campoFormularioVersion->getEntidad()->getCampoVisualizar();
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
            $detalleValor[] = $resultado->$get();
        }
        $this->buildResumen($campoFormularioVersion, implode(" ", $detalleValor));
    }

    protected function saveOpcion($clase, $registroFormulario, $campoFormularioVersion, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById($valor);


        if ($detalleLista != null) {
            $nombreClase = "App\Entity\RegistroLista";
            $entidad = new $nombreClase();
            $entidad->setRegistro($registroFormulario);
            $entidad->setCampoFormularioVersion($campoFormularioVersion);
            $entidad->setDetalleLista($detalleLista);
            $entidad->setEstadoId($estado_id);
            $this->em->persist($entidad);
            $this->buildResumen($campoFormularioVersion, $detalleLista->getDescripcion());
        }
    }

    protected function saveLista($clase, $registroFormulario, $campoFormularioVersion, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById($valor);

        if ($detalleLista != null) {
            $nombreClase = "App\Entity\Registro" . $clase;
            $entidad = new $nombreClase();
            $entidad->setRegistro($registroFormulario);
            $entidad->setCampoFormularioVersion($campoFormularioVersion);
            $entidad->setDetalleLista($detalleLista);
            $entidad->setEstadoId($estado_id);
            $this->em->persist($entidad);
            $this->buildResumen($campoFormularioVersion, $valor);
        }
        return ($detalleLista == null) ? null : $detalleLista->getDescripcion();
    }

    protected function saveCampo($registroFormulario, $campoFormularioVersion, $registroPadre, $estado_id)
    {
        //Buscar el campo FormularioVersion Id relacionado al campoFormularioVersion enviado
        $campoFormularioOrigenId = $campoFormularioVersion->getCampoFormularioVersionId();

        //Buscar el campoFormularioVersion relacionado al campoFormularioId
        $campoFormularioOrigen = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioOrigenId);
        $campoFormularioRelacionado = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array('formulario_version_id' => $registroPadre->getFormularioVersionId(), 'campo_formulario_id' => $campoFormularioOrigen->getCampoFormularioId()));

        if (count($campoFormularioRelacionado) > 0) {
            $campoFormularioRelacionadoId = $campoFormularioRelacionado[0]->getId();
            $tipoCampo = $campoFormularioRelacionado[0]->getTipoCampo();
            //Consulto el valor basado en tu tipo_campo
            $queryBuilder = $this->em->createQueryBuilder();
            $result = $queryBuilder
                ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
                ->from('App\\Entity\\Registro' . $tipoCampo, 'r')
                ->where("r.campo_formulario_version_id = :campo_formulario_id")
                ->andWhere("r.estado_id = :estado_id")
                ->andWhere("r.registro_id = :registro_id")
                ->setParameter('campo_formulario_id', $campoFormularioRelacionadoId)
                ->setParameter('estado_id', 1)
                ->setParameter('registro_id', $registroPadre->getId())
                ->getQuery()
                ->execute();

                //extraer id_campo y registro_origen_id de la variable id
            if (count($result) == 1) {
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
        }
        

        
    }

    protected function buildResumen($campoFormularioVersion, $valor)
    {
        if ($campoFormularioVersion->getMostrarFront() === true) {
            $this->mostrarFront[$campoFormularioVersion->getValorCuadroTexto()] = $valor;
            $this->busqueda[$campoFormularioVersion->getCampo()] = $valor;
        }
    }

    protected function crearRadicadoElectronico($registroFormulario)
    {
        if ($registroFormulario->getFormularioVersion()->getRadicadoElectronico() == true) {
            $output = StickerGenerator::print($this->em, $registroFormulario, "radicadoelectronico");
            $gestorArchivo = new GestorArchivos();
            $folder = date("Ymd");
            $fileLocation = $_ENV['TMP_LOCATION'] . TextUtils::slugify($registroFormulario->getId() . " " . $registroFormulario->getFormularioVersion()->getNombre()) . '-' . date("Ymdhis") . '.pdf';
            file_put_contents($fileLocation, $output);
            $mime_type = mime_content_type($fileLocation);
            // $fileToUpload=new File($fileLocation);
            $fileToUpload = new UploadedFile($fileLocation, date("Ymdhis") . '.pdf', $mime_type, null, true);
            $result = $gestorArchivo->uploadFile($this->em, $fileToUpload, $folder, $_ENV['FILE_LOCATION']);
            $archivo = new Archivo();
            // se setean todos los valores
            $archivo->setRegistro($registroFormulario);
            $archivo->setVersion("1");
            $archivo->setFechaVersion(new \DateTime());
            $archivo->setComentario("Radicado Electrónico");
            $archivo->setEstadoId(1);
            $archivo->setNombre($fileToUpload->getClientOriginalName());
            $archivo->setIdentificador($result["gDriveFileSavedID"]);
            $archivo->setCarpeta($result["carpeta"]);
            if(isset($this->ejecucion_paso_id)){
                $saveFileEjecucion = $this->em->getRepository(EjecucionPaso::class)->findOneById($this->ejecucion_paso_id);
                $saveFileEjecucion->setFile('1');
                $this->em->persist($saveFileEjecucion);
                $archivo->setEjecucionPasoId($this->ejecucion_paso_id);
            }
            $this->em->persist($archivo);
            //Se hace la relación entre formato y archivo
            $this->em->flush();
        }
    }
}
