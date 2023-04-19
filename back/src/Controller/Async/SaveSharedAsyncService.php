<?php

namespace App\Controller\Async;

use App\Entity\Compartido;
use App\Entity\Registro;
use App\Entity\Plantilla;
use App\Entity\PasoEventoVersion;
use App\Entity\PlantillaVersion;
use App\Entity\RegistroMultiseleccion;
use App\Entity\CampoFormularioVersion;
use App\Utils\Auditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpKernel\KernelInterface;
use \DateTime;

/**
 * Undocumented class
 */
class SaveSharedAsyncService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, KernelInterface $kernel)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->kernel = $kernel;
    }



    public function  saveEmail($destinatarios, $asunto, $contenido, $Adjuntos, $typeNotificacion, $registro)
    {
        $usuario = $this->tokenStorage->getToken()->getUser();
        $compartido = new Compartido();
        $cuando = new DateTime();
        $registro = $this->em->getRepository(Registro::class)->findOneById($registro);
        $compartido->setCuando($cuando);
        $compartido->setPara($destinatarios);
        $compartido->setAsunto($asunto);
        $compartido->setContenido($contenido);
        $compartido->setRegistro($registro);
        $compartido->setDescripcionAdjuntos($Adjuntos);
        $compartido->setEstadoId(1);
        $compartido->setTipoNotificacion($typeNotificacion);
        $this->em->persist($compartido);
        $this->em->flush();

        if ($_ENV["COMMAND_FROM_CONTROLLER"] == "true") {
            $application = new Application($this->kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput([
                'command' => 'gdocument:send-mail',
            ]);

            $output = new NullOutput();
            $application->run($input, $output);
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $adjuntos = $queryBuilder
            ->select('a.nombre')
            ->from('App\\Entity\\Archivo', 'a')
            ->where("a.id IN (:adjuntos)")
            ->setParameter('adjuntos', json_decode($Adjuntos))
            ->getQuery()
            ->execute();
        $archivoAdjuntosArray = array();
        foreach ($adjuntos as $adjunto) {
            $archivoAdjuntosArray[] = $adjunto["nombre"];
        }        
        $archivoAdjuntos=implode(" ",$archivoAdjuntosArray);
        //Registro Auditoria
        $valor_actual = array(
            "Radicado" => $registro->getId(),
            "Asunto" => $asunto,
            "Adjuntos" => $archivoAdjuntos,
            "Fecha" => $cuando->format("Y-m-d H:i:s"),
        );
        Auditor::registerAction($this->em, $registro, $usuario, null, $valor_actual, $typeNotificacion);
        return array(["response" => "true"]);

    }

    public function setPlantilla($data,$dataEvento) {
        $registro = $this->em->getRepository(Registro::class)->findOneById($data->{'registroId'});
        $registroVersionId = $registro->getFormularioVersionId();
        $plantillaVersion = $this->em->getRepository(PlantillaVersion::class)->findOneBy(['formulario_version_id' => $registroVersionId,'descripcion'=>$dataEvento['plantilla_name']]);
        $plantilla = $this->em->getRepository(PlantillaVersion::class)->findOneById($plantillaVersion->getId());
        $plantillasFormulario = $registro->getFormularioVersion()->getPlantillasVersion();
        $plantillarelacionada = 0;
        if (null !== $plantilla) {
            foreach ($plantillasFormulario as $plantillaFormulario) {
                if ($plantillaFormulario->getId() == $plantilla->getId()) {
                    $plantillarelacionada = 1;
                }
            }
            if (isset($registro)) {
                if ($plantillarelacionada == 1) {
                    //guardar el contenido de la plantilla en una variable
                    $contenidoPlantilla = $plantilla->getContenido();
                    // Consultar los campos que pertenecen al formulario del registro.

                    //reemplazar los tokens de la plantilla por los valores de los campos del registro
                    $camposDiligenciados = array();
                    foreach ($registro->getRegistroEntidads() as $registroEntidad) {
                        $camposDiligenciados[] = $registroEntidad;
                    }
                    foreach ($registro->getRegistroCampos() as $registroCampo) {
                        $camposDiligenciados[] = $registroCampo;
                    }
                    foreach ($registro->getRegistroFechas() as $registroFecha) {
                        $camposDiligenciados[] = $registroFecha;
                    }
                    foreach ($registro->getRegistroHoras() as $registroHora) {
                        $camposDiligenciados[] = $registroHora;
                    }
                    foreach ($registro->getRegistroListas() as $registroLista) {
                        $camposDiligenciados[] = $registroLista;
                    }
                    foreach ($registro->getRegistroMultiseleccions() as $registroMultiseleccion) {
                        //verificar si el registro_id ya esta almacenado
                        if (!array_key_exists($registroMultiseleccion->getRegistro()->getId(), $camposDiligenciados)) {
                            $camposDiligenciados[$registroMultiseleccion->getRegistro()->getId()] = $registroMultiseleccion;
                        }

                    }
                    foreach ($registro->getRegistroNumericoEnteros() as $registroNumericoEntero) {
                        $camposDiligenciados[] = $registroNumericoEntero;
                    }
                    foreach ($registro->getRegistroNumericoDecimals() as $registroNumericoDecimal) {
                        $camposDiligenciados[] = $registroNumericoDecimal;
                    }
                    foreach ($registro->getRegistroNumericoMonedas() as $registroNumericoMoneda) {
                        $camposDiligenciados[] = $registroNumericoMoneda;
                    }
                    foreach ($registro->getRegistroTextoCortos() as $registroTextoCorto) {
                        $camposDiligenciados[] = $registroTextoCorto;
                    }
                    foreach ($registro->getRegistroTextoLargos() as $registroTextoLargo) {
                        $camposDiligenciados[] = $registroTextoLargo;
                    }
                    foreach ($camposDiligenciados as $campoDiligenciado) {
                        switch ($campoDiligenciado->getCampoFormularioVersion()->getTipoCampo()) {
                            case "Opcion":
                            case "Multiseleccion":
                                //saber que lista se selecciono
                                $campoFormularioVersionId = $campoDiligenciado->getCampoFormularioVersion()->getId();
                                $campo = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $registrosMultiseleccion = $this->em->getRepository(RegistroMultiseleccion::class)->findBy(array("campo_formulario_version_id" => $campoFormularioVersionId));
                                $valor = array();
                                foreach ($registrosMultiseleccion as $registroMultiseleccion) {
                                    $valor[] = $registroMultiseleccion->getDetalleLista()->getDescripcion();
                                }
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = $campo;
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = implode(", ", $valor);
                                // var_dump($valoresReemplazar);
                                // die;
                                break;
                            case "Lista":
                                //saber que lista se selecciono
                                //$campo = $campoDiligenciado->getCampoFormularioVersion()->getLista()->getNombre();
                                $campo = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $valor = $campoDiligenciado->getDetalleLista()->getDescripcion();
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = $campo;
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = $valor;
                                break;
                            case "Entidad":
                                //Ir entidad campo_formulario y consultar la entidad que corresponde
                                $nombre = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $campo = $campoDiligenciado->getCampoFormularioVersion()->getEntidad()->getNombre();
                                $camposVisualizarEntidad = $campoDiligenciado->getCampoFormularioVersion()->getEntidad()->getCampoVisualizar();
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
                                $resultado = $manager->findOneBy(array("id" => $campoDiligenciado->getIdEntidad()));
                                $campos = explode("+", str_replace("-", "", $camposVisualizarEntidad));
                                $detalleValor = array();
                                foreach ($campos as $detalleCampo) {
                                    $get = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $detalleCampo)));
                                    if(isset($resultado)) {
                                        $detalleValor[] = $resultado->$get();
                                    } else {
                                        $detalleValor[] = "";
                                    }
                                }
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = strtolower($nombre);
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = implode(" ", $detalleValor);
                                break;
                            case "Formulario":
                            case "FormularioVersion":
                                //Consultar el formulario relacionado
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = $campoDiligenciado->getValor();                                
                                break;
                            case "Hora":
                                if ($campoDiligenciado->getValor() instanceof \DateTime) {
                                    $valor = $campoDiligenciado->getValor()->format('H:i:s');
                                }
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = $valor;
                                break;
                            case "Fecha":
                                if ($campoDiligenciado->getValor() instanceof \DateTime) {
                                    $valor = $campoDiligenciado->getValor()->format('Y-m-d');
                                }
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = $valor;
                                break;
                            case "TextoCorto":
                            case "TextoLargo":
                            case "NumericoMoneda":
                            case "NumericoDecimal":
                            case "NumericoEntero":
                                $valor = $campoDiligenciado->getValor();
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["nombre"] = $campoDiligenciado->getCampoFormularioVersion()->getCampo();
                                $valoresReemplazar[$campoDiligenciado->getCampoFormularioVersion()->getId()]["valor"] = $valor;
                                break;

                        }
                    }
                    if (isset($valoresReemplazar)) {
                        foreach ($valoresReemplazar as $valorReemplazar) {
                            $buscar = "<" . $valorReemplazar["nombre"] . ">";
                            $reemplazar = $valorReemplazar["valor"];
                            $contenidoPlantilla = str_replace($buscar, $reemplazar, $contenidoPlantilla);
                        }
                    }
                    return $this->saveEmail(
                        $dataEvento['correos'],
                        'Correo Automatico Gdocument',
                        $contenidoPlantilla,
                        '[]',
                        $data->{'method'},
                        $data->{'registroId'} 
                    );

                } else {
                    return array("response" => "PlantillaVersion no esta relacionada con el Formulario que se creo el Registro");
                }
            } else {
                return array("response" => "Registro no encontrado");
            }
        } else {
            return array("response" => "PlantillaVersion no existe");
        }
    }

    public function save(Request $request){

        $data = json_decode($request->getContent());

        if(
        isset($data->{'registroId'}) &&
        isset($data->{'method'})  &&
        isset($data->{'sharedButtonId'})
        ){
            $eventoThird = $this->em->getRepository(PasoEventoVersion::class)->findOneById($data->{'sharedButtonId'});
            $dataEvento = $eventoThird->getConfig();
            if($data->{'method'} == 'emailthird'){
                return $this->saveEmail(
                    $dataEvento['correos'],
                    $dataEvento['asunto'],
                    $dataEvento['contenido'],
                    '[]',
                    $data->{'method'},
                    $data->{'registroId'} 
                );
            }else if($data->{'method'} == 'emailplant'){
                return $this->setPlantilla($data,$dataEvento);
            }
        } else {
            return array(["response" => "false"]);
        }
        return array(["response" => "true"]);
    }


}