<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\Entidad;
use App\Entity\CampoFormularioVersion;
use App\Entity\DetalleLista;
use App\Entity\EjecucionPaso;
use App\Entity\EjecucionFlujo;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use App\Entity\RegistroCampo;
use App\Entity\RegistroEntidad;
use App\Entity\Sede;
use App\Entity\TipoCorrespondencia;
use App\Utils\GestorArchivos;
use App\Utils\StickerGenerator;
use App\Utils\TextUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Exceptions\FormException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Undocumented class
 */
class RegistroFormularioVersionService
{
    private $_em;
    private $_registroFormularioVersionStandard;
    private $_childNodes;
    private $tokenStorage;
    private $mostrarFront;
    private $busqueda;

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
     * @param string $login
     *
     * @return registroformularioVersionStandard
     */
    public function save($formularioVersionId, $estadoId, $ejecucionFlujoId, $tipoCorrespondencia, $ejecucionPasoId, $registros, $registroId, $startWorkFlow = false)
    {
        //Recordar cambiar campo fecha_hora de registro por un tipo datetime
        $fecha_hora = new \DateTime();

        $respuestas = [];
        if(isset($registros->{"respuestas"})) {
            $respuestas = $registros->{"respuestas"};
        }

        //Primero capturo la petición json de guardado de un registro de un formularioVersion
        if ($registroId != null) {
            $registroFormularioVersion = $this->em->getRepository(Registro::class)
                ->findOneById($registroId);
            
            $registroFormularioVersion->setStartWorkFlow(false);
        } else {
            $registroFormularioVersion = new Registro();
            $registroFormularioVersion->setStartWorkFlow($startWorkFlow);
        }
        //cargo primero el objeto formularioVersion

        try {
            $this->validateIndice($respuestas, $registroId);

            $mostrarFront = array();

            $formularioVersion = $this->em->getRepository(FormularioVersion::class)
                ->findOneById($formularioVersionId);
            $camposFormularioVersion = $formularioVersion->getCampoFormulariosVersion();
            $idsCampoFormularioVersion = array();
            foreach ($camposFormularioVersion as $campoFormularioVersion) {
                if ($campoFormularioVersion->getEstadoId() == 1) {
                    $idsCampoFormularioVersion[$campoFormularioVersion->getId()] = $campoFormularioVersion->getId();
                }
            }
            $usuario = $this->tokenStorage->getToken()->getUser();
            $registroFormularioVersion->setFechaFormularioVersion($formularioVersion->getFechaVersion());
            $registroFormularioVersion->setNombreFormulario($formularioVersion->getNombre());
            $registroFormularioVersion->setNomenclaturaFormulario($formularioVersion->getNomenclaturaFormulario());
            $registroFormularioVersion->setFechaHora($fecha_hora);
            $registroFormularioVersion->setEstadoId($estadoId);
            $registroFormularioVersion->setUsuario($usuario);
            //Si el usuario tiene una sede diferente a la que tiene registrada en el proceso
            // colocar la sede asignada por el usuario
            if ($usuario->getSede() != $usuario->getProceso()->getSede() && null !== $usuario->getSede()) {
                $sedeUsuario = $usuario->getSede();
            } else if ($usuario->getSede() == $usuario->getProceso()->getSede() || null === $usuario->getSede()) {
                $sedeUsuario = $usuario->getProceso()->getSede();
            }
            if ($tipoCorrespondencia != null) {
                $tipoCorrespondenciaId = $tipoCorrespondencia;
            } else {
                $tipoCorrespondenciaId = 1;
            }

            if ($ejecucionFlujoId != null) {
                $ejecucionFlujo = $this->em->getRepository(EjecucionFlujo::class)->findOneById($ejecucionFlujoId);
                $registroFormularioVersion->setEjecucionFlujo($ejecucionFlujo);

                if ($ejecucionPasoId != null) {
                    $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
                    $ejecucionPaso->setFillForm(true);
                }
            } else if ($ejecucionPasoId != null) {
                $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
                $registroFormularioVersion->setEjecucionPaso($ejecucionPaso);
            }
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
            $registroFormularioVersion->setConsecutivo($tipoCorrespondencia->getConsecutivo());
            $registroFormularioVersion->setTipoCorrespondencia($tipoCorrespondencia->getId());
            $registroFormularioVersion->setSede($sedeUsuario->getNombre());

            //Agregar respuestas vacias a campos que no se diligenciaron en el formularioVersion
            foreach($respuestas as $registro){
                unset($idsCampoFormularioVersion[$registro->{"campo_formulario_version_id"}]);
            }
            //Seteo los identificadores restantes en el arreglo de respuestas como valores vacios
            foreach($idsCampoFormularioVersion as $idCampoFormularioVersion){
                $respuestavacia = array();
                $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)
                    ->findOneById($idCampoFormularioVersion);
                $respuestavacia["campo_formulario_version_id"]=$idCampoFormularioVersion;
                $respuestavacia["valor"]="";
                $respuestavacia["campo"]=$campoFormularioVersion->getTipoCampo();
                $respuestavacia["idRegistro"]=null;
                $respuestas[] = (object)$respuestavacia;
            }

            foreach ($respuestas as $registro) {
                //Cargo el tipo de registro basado en campo_formulario_version_id
                $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)
                    ->findOneById($registro->{"campo_formulario_version_id"});
                //TODO: RegistroBoolean
                switch ($campoFormularioVersion->getTipoCampo()) {
                    case "Booleano":
                    case "TextoCorto":
                    case "TextoLargo":
                    case "NumericoMoneda":
                    case "NumericoDecimal":
                    case "NumericoEntero":

                        $valor = $registro->{"valor"};
                        if(($campoFormularioVersion->getTipoCampo() == "NumericoMoneda" 
                        || $campoFormularioVersion->getTipoCampo() == "NumericoDecimal"
                        || $campoFormularioVersion->getTipoCampo() == "NumericoEntero") && $valor == "") {
                            $valor = null;
                        }
                        $this->savePrimitive($campoFormularioVersion->getTipoCampo(), $valor, $registroFormularioVersion, $campoFormularioVersion, 1);
                        break;
                    case "Hora":
                        if ($registro->{"valor"} != "") {
                            $time = new \DateTime($registro->{"valor"});
                        } else {
                            $time = null;
                        }
                        
                        $this->saveDate($campoFormularioVersion->getTipoCampo(), $time, $registroFormularioVersion, $campoFormularioVersion, 1);
                        break;
                    case "Fecha":
                        if ($registro->{"valor"} != "") {
                            $date = new \DateTime($registro->{"valor"});
                        } else {
                            $date = null;
                        }
                        
                        $this->saveDate($campoFormularioVersion->getTipoCampo(), $date, $registroFormularioVersion, $campoFormularioVersion, 1);
                        break;
                    case "Entidad":
                        if (isset($registro->{"valor"})) {
                            $this->saveEntidad($registroFormularioVersion, $campoFormularioVersion, $registro->{"valor"}, 1);
                        } else {
                            $this->saveEntidad($registroFormularioVersion, $campoFormularioVersion, "", 1);
                        }
                        break;
                    case "FormularioVersion":
                    case "Formulario":
                        if (isset($registro->{"id"}) && isset($registro->{"valor"})) {
                            $this->saveCampo($registroFormularioVersion, $campoFormularioVersion, $registro->{"id"}, $registro->{"valor"}, 1);
                        } else {
                            $this->saveCampo($registroFormularioVersion, $campoFormularioVersion, null, "", 1);
                        }
                        
                        break;
                    case "Opcion":
                        if (isset($registro->{"valor"}) && $registro->{"valor"} != "") {
                            $this->saveOpcion($campoFormularioVersion->getTipoCampo(), $registroFormularioVersion, $campoFormularioVersion, $registro->{"valor"}, 1);
                        }
                        break;
                    case "Multiseleccion":
                        $descripcionValores = array();
                        if (isset($registro->{"valor"}) &&  $registro->{"valor"} != "") {
                            $valores = $registro->{"valor"};
                            foreach ($valores as $valor) {
                                $descripcionValores[] = $this->saveLista($campoFormularioVersion->getTipoCampo(), $registroFormularioVersion, $campoFormularioVersion, $valor->{"id"}, 1);
                            }
                        }
                        
                        $this->buildResumen($campoFormularioVersion, implode(", ", $descripcionValores));
                        break;
                    case "Lista":
                        if (isset($registro->{"valor"}) && $registro->{"valor"} != "") {
                            $descripcionValor = $this->saveLista($campoFormularioVersion->getTipoCampo(), $registroFormularioVersion, $campoFormularioVersion, $registro->{"valor"}, 1);
                        } else {
                            $descripcionValor = "";
                        }

                        $this->buildResumen($campoFormularioVersion, $descripcionValor);
                        break;
                }
            }

            $registroFormularioVersion->setResumen($this->mostrarFront);
            $registroFormularioVersion->setBusqueda($this->busqueda);
            $registroFormularioVersion->setFormularioVersion($formularioVersion);
            $this->em->persist($registroFormularioVersion);

            //Luego de un guardado exitoso del registro incrementar en 1 el consecutivo usado
            $tipoCorrespondencia->incrementarConsecutivo();
            $this->em->persist($tipoCorrespondencia);

            $this->em->flush();

            $radicado = $formularioVersion->getId() . '-' . $sedeUsuario->getId() . '-' . $registroFormularioVersion->getId();
            $registroFormularioVersion->setRadicado($radicado);
            $this->em->persist($registroFormularioVersion);
            $this->em->flush();

            //Verificar si el formularioVersion tiene la opción radicado electrónico seleccionada.
            // Si es asi ejecutar la acción que genera el sticker de radicado electrónico
            self::crearRadicadoElectronico($registroFormularioVersion);
        } catch (\Exception $e) {
            $message = array("return" => array("response" => $e->getMessage()));
            return $message;
        }
        return $registroFormularioVersion;
    }

    protected function validateIndice($respuestas, $registroId) {
        $camposFormularioVersion = [];

        foreach ($respuestas as $registro) {
            $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)
            ->findOneById($registro->{"campo_formulario_version_id"});

            if($campoFormularioVersion->getIndice() == true) {
                $camposFormularioVersion[] = $campoFormularioVersion;
                $campoFormularioId = $campoFormularioVersion->getId();
                $valorCuadroTexto = $campoFormularioVersion->getValorCuadroTexto();

                $value = $registro->{"valor"};

                if($campoFormularioVersion->getTipoCampo() == "Entidad") {
                    $value = $this->getValueEntidad($campoFormularioVersion->getEntidadId(), $value, $campoFormularioVersion);
                }
                
                $result = $this->em->getRepository(Registro::class)->findIndexValue($this->em, $value, $campoFormularioId, $valorCuadroTexto);

                if(count($result) > 0 && $registroId != $result[0]["registroId"]) {
                    throw new FormException(Response::HTTP_PRECONDITION_FAILED, "Ya existe un registro con el valor ". $result[0]["valor"] . ", este campo no permite otros registros con el mismo valor");
                }
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

    protected function crearRadicadoElectronico($registroFormularioVersion)
    {
        if (strpos($registroFormularioVersion->getFormularioVersion()->getTipoSticker(), "radicadoelectronico") != false) {
            $output = StickerGenerator::print($this->em, $registroFormularioVersion, "radicadoelectronico");
            $gestorArchivo = new GestorArchivos();
            $folder = date("Ymd");
            $fileLocation = $_ENV['TMP_LOCATION'] . TextUtils::slugify($registroFormularioVersion->getId() . " " . $registroFormularioVersion->getFormularioVersion()->getNombre()) . '-' . date("Ymdhis") . '.pdf';
            file_put_contents($fileLocation, $output);
            $mime_type = mime_content_type($fileLocation);
            // $fileToUpload=new File($fileLocation);
            $fileToUpload = new UploadedFile($fileLocation, date("Ymdhis") . '.pdf', $mime_type, null, true);
            $result = $gestorArchivo->uploadFile($this->em, $fileToUpload, $folder, $_ENV['FILE_LOCATION']);
            $archivo = new Archivo();
            // se setean todos los valores
            $archivo->setRegistro($registroFormularioVersion);
            $archivo->setVersion("1");
            $archivo->setFechaVersion(new \DateTime());
            $archivo->setComentario("Radicado Electrónico");
            $archivo->setEstadoId(1);
            $archivo->setNombre($fileToUpload->getClientOriginalName());
            $archivo->setIdentificador($result["gDriveFileSavedID"]);
            $archivo->setCarpeta($result["carpeta"]);
            $this->em->persist($archivo);
            //Se hace la relación entre formato y archivo
            $this->em->flush();
        }
    }

    protected function saveDate($clase, $valor, $registroFormularioVersion, $campoFormularioVersion, $estado_id)
    {
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = $this->em->getRepository($nombreClase)
        ->findOneBy(array("registro_id" => $registroFormularioVersion->getId(), "campo_formulario_version_id" => $campoFormularioVersion->getId()));
        if($entidad == null) {
            $entidad = new $nombreClase();
        }
        $entidad->setRegistro($registroFormularioVersion);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setValor($valor);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);

        if ($valor == null) {
            $this->buildResumen($campoFormularioVersion, "");
        } else {
            if ($clase == "Fecha") {
                $this->buildResumen($campoFormularioVersion, $valor->format("Y-m-d"));
            }
            if ($clase == "Hora") {
                $this->buildResumen($campoFormularioVersion, $valor->format("H:i"));
            }
        }
    }

    protected function savePrimitive($clase, $valor, $registroFormularioVersion, $campoFormularioVersion, $estado_id)
    {
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = $this->em->getRepository($nombreClase)
        ->findOneBy(array("registro_id" => $registroFormularioVersion->getId(), "campo_formulario_version_id" => $campoFormularioVersion->getId()));
        if($entidad == null) {
            $entidad = new $nombreClase();
        }
        $entidad->setRegistro($registroFormularioVersion);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setValor($valor);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, $valor);
    }

    protected function saveEntidad($registroFormularioVersion, $campoFormularioVersion, $id_entidad, $estado_id)
    {
        $entidad = $this->em->getRepository(RegistroEntidad::class)
        ->findOneBy(array("registro_id" => $registroFormularioVersion->getId(), "campo_formulario_version_id" => $campoFormularioVersion->getId()));
        if($entidad == null) {
            $entidad = new RegistroEntidad();
        }
        $entidad->setRegistro($registroFormularioVersion);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setIdEntidad($id_entidad);
        $entidad->setEstadoId($estado_id);

        //Guardar en resumen el valor del campo Entidad relacionado
        $campo = $campoFormularioVersion->getEntidad()->getNombre();
        /*switch ($campo) {
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
        }*/
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

        $this->buildResumen($campoFormularioVersion, $valor);
        $entidad->setValor($valor);
        $this->em->persist($entidad);
    }

    protected function saveOpcion($clase, $registroFormularioVersion, $campoFormularioVersion, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById($valor);
        $nombreClase = "App\Entity\RegistroLista";
        $entidad = $this->em->getRepository($nombreClase)
        ->findOneBy(array("registro_id" => $registroFormularioVersion->getId(), "campo_formulario_version_id" => $campoFormularioVersion->getId()));
        if($entidad == null) {
            $entidad = new $nombreClase();
        }
        $entidad->setRegistro($registroFormularioVersion);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setDetalleLista($detalleLista);
        $entidad->setEstadoId($estado_id);
        $this->em->persist($entidad);

        $this->buildResumen($campoFormularioVersion, $detalleLista->getDescripcion());
    }

    protected function saveLista($clase, $registroFormularioVersion, $campoFormularioVersion, $valor, $estado_id)
    {
        $detalleLista = $this->em->getRepository(DetalleLista::class)
            ->findOneById($valor);
        $nombreClase = "App\Entity\Registro" . $clase;
        $entidad = $this->em->getRepository($nombreClase)
        ->findOneBy(array("registro_id" => $registroFormularioVersion->getId(), "campo_formulario_version_id" => $campoFormularioVersion->getId()));
        if($entidad == null) {
            $entidad = new $nombreClase();
        }
        $entidad->setRegistro($registroFormularioVersion);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);
        $entidad->setDetalleLista($detalleLista);
        $entidad->setEstadoId($estado_id);
        $entidad->setValor($detalleLista->getDescripcion());
        $this->em->persist($entidad);
        return $detalleLista->getDescripcion();
        $this->buildResumen($campoFormularioVersion, $valor);
    }

    protected function saveCampo($registroFormularioVersion, $campoFormularioVersion, $id, $valor, $estado_id)
    {
        //Guardar valores nuevos en entidad RegistroCampo
        $entidad = $this->em->getRepository(RegistroCampo::class)
        ->findOneBy(array("registro_id" => $registroFormularioVersion->getId(), "campo_formulario_version_id" => $campoFormularioVersion->getId()));
        if($entidad == null) {
            $entidad = new RegistroCampo();
        }
        $entidad->setRegistro($registroFormularioVersion);
        $entidad->setValor($valor);
        $entidad->setEstadoId($estado_id);
        $entidad->setCampoFormularioVersion($campoFormularioVersion);

        //extraer id_campo y registro_origen_id de la variable id
        if ($id != null) {
            $ids = explode("-", $id);
            $id_campo = $ids[0];
            $registro_origen_id = $ids[1];
            $entidad->setIdCampo($id_campo);
            $entidad->setRegistroIdOrigen($registro_origen_id);
        }

        $this->em->persist($entidad);
        $this->buildResumen($campoFormularioVersion, $valor);
    }

    protected function buildResumen($campoFormularioVersion, $valor)
    {
        if ($campoFormularioVersion->getMostrarFront() === true) {
            $this->mostrarFront[$campoFormularioVersion->getValorCuadroTexto()] = strval($valor);
            $this->busqueda[TextUtils::slugifyWithUnderscore($campoFormularioVersion->getValorCuadroTexto())] = strval($valor);
        }
    }
}
