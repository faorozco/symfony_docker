<?php

namespace App\Controller;

use App\Entity\CampoFormularioVersion;
use App\Entity\DetalleLista;
use App\Entity\Registro;
use App\Entity\RegistroCampo;
use App\Entity\RegistroEntidad;
use App\Entity\RegistroFecha;
use App\Entity\RegistroHora;
use App\Entity\RegistroLista;
use App\Entity\RegistroMultiseleccion;
use App\Entity\RegistroNumericoDecimal;
use App\Entity\RegistroNumericoEntero;
use App\Entity\RegistroNumericoMoneda;
use App\Entity\RegistroTextoCorto;
use App\Entity\RegistroTextoLargo;
use App\Entity\RegistroBooleano;
use App\Entity\EjecucionPaso;
use App\Utils\Auditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use \DateTime;
use App\Exceptions\FormException;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Entidad;

/**
 * Undocumented class
 */
class CamposFormularioUpdateService
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
    }

    public function Get($respuestas, $ejecucionPasoId)
    {

        try {

            $usuario = $this->tokenStorage->getToken()->getUser();

            $respuestas = (array) $respuestas;

            $id = $respuestas['registro'];

            $mostrarFront = null;
            $resumen = null;

            $this->validateIndice($respuestas['respuestas'], $id);

            $registroOriginal = $this->em->getRepository(Registro::class)->findOneById($id);
            $valores = array();
            foreach ($respuestas['respuestas'] as $respuesta) {

                $respuesta = (array) $respuesta;

                switch ($respuesta['campo']) {

                    case 'Fecha':

                        if ($respuesta['idRegistro']) {

                            $registro = $this->em->getRepository(RegistroFecha::class)->findOneById($respuesta['idRegistro']);

                            $valorAnterior = $registro->getValor();
                            if ($respuesta['valor'] == "" && $valorAnterior == null) {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = null;
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = null;
                            } else {
                                if ($respuesta['valor'] != "") {
                                    $nuevaFecha = (new DateTime($respuesta['valor']))->format("Y-m-d");
                                } else {
                                    $nuevaFecha = $respuesta['valor'];
                                }

                                if ($registro->getValor() != null) {
                                    if ($registro->getValor() instanceof DateTime) {
                                        $oldFecha = $registro->getValor()->format("Y-m-d");
                                    } else {
                                        $oldFecha = (new DateTime($registro->getValor()))->format("Y-m-d");
                                    }
                                } else {
                                    $oldFecha = null;
                                }

                                if ($nuevaFecha != $oldFecha) {
                                    $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $nuevaFecha;
                                    $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $oldFecha;
                                }
                                if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                    $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $nuevaFecha;
                                    $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $nuevaFecha;
                                }

                                if ($respuesta['valor'] == "") {
                                    $registro->setValor(null);
                                } else {
                                    $registro->setValor(new \DateTime($respuesta['valor']));
                                }

                                $this->em->persist($registro);
                            }
                        }
                        break;
                    case 'Entidad':

                        if ($respuesta['idRegistro']) {

                            $registro = $this->em->getRepository(RegistroEntidad::class)->findOneById($respuesta['idRegistro']);

                            $entidad = $registro->getCampoFormularioVersion()->getEntidad()->getNombre();
                            $entidadId = $registro->getIdEntidad();

                            if($registro->getCampoFormularioVersion()->getEntidadColumnName() == null) {
                                $camposBusqueda = $registro->getCampoFormularioVersion()->getEntidad()->getCampoVisualizar();
                                $camposBusqueda = explode("-", $camposBusqueda);
                                $camposBusqueda = explode("+", $camposBusqueda[1]);
                            } else {
                                $camposBusqueda[] = $registro->getCampoFormularioVersion()->getEntidadColumnName();
                            }

                            
                            //Reinicio de $camposBusquedaTMP
                            $camposBusquedaTMP = array();
                            foreach ($camposBusqueda as $campoBusqueda) {
                                $camposBusquedaTMP[] = "e." . $campoBusqueda;
                            }
                            if (count($camposBusquedaTMP) > 1) {
                                $camposBusqueda = "CONCAT(" . implode(", ", $camposBusquedaTMP) . ")";
                            } else {
                                $camposBusqueda = implode(", ", $camposBusquedaTMP);
                            }

                            $queryBuilder = $this->em->createQueryBuilder();

                            if (isset($respuesta['valor'])) {
                                $result = $queryBuilder
                                    ->select($camposBusqueda . " as valor")
                                    ->from('App\\Entity\\' . $entidad, 'e')
                                    ->where("e.id = :entidad_id")
                                    ->setParameter('entidad_id', $respuesta['valor'])
                                    ->getQuery()
                                    ->execute();
                            } else {
                                $result = array();
                                $arrayValue = array();
                                $arrayValue["valor"] = "";
                                $result[] = $arrayValue;
                            }

                            $queryBuilderAnterior = $this->em->createQueryBuilder();
                            $resultAnterior = $queryBuilderAnterior
                                ->select($camposBusqueda . " as valor")
                                ->from('App\\Entity\\' . $entidad, 'e')
                                ->where("e.id = :entidad_id")
                                ->setParameter('entidad_id', $registro->getIdEntidad())
                                ->getQuery()
                                ->execute();

                            if (count($resultAnterior) == 0) {
                                $arrayValue = array();
                                $arrayValue["valor"] = "";
                                $resultAnterior[] = $arrayValue;
                            }

                            if ($resultAnterior[0]["valor"] != $result[0]["valor"]) {
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $resultAnterior[0]["valor"];
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $result[0]["valor"];
                            }

                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $result[0]["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $result[0]["valor"];
                            }
                            $registro->setValor($result[0]["valor"]);

                            if (isset($respuesta['valor'])) {
                                $registro->setIdEntidad($respuesta['valor']);
                            } else {
                                $registro->setIdEntidad(null);
                            }


                            $this->em->persist($registro);
                        }

                        break;
                    case 'NumericoMoneda':

                        if ($respuesta['idRegistro']) {
                            $registro = $this->em->getRepository(RegistroNumericoMoneda::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }

                            if ($respuesta['valor'] == "") {
                                $registro->setValor(null);
                            } else {
                                $registro->setValor($respuesta['valor']);
                            }

                            $this->em->persist($registro);
                        }

                        break;
                    case 'NumericoEntero':
                        if ($respuesta['idRegistro']) {
                            $registro = $this->em->getRepository(RegistroNumericoEntero::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                            if ($respuesta['valor'] == "") {
                                $registro->setValor(null);
                            } else {
                                $registro->setValor($respuesta['valor']);
                            }
                            $this->em->persist($registro);
                        }

                        break;
                    case 'NumericoDecimal':

                        if ($respuesta['idRegistro']) {
                            $registro = $this->em->getRepository(RegistroNumericoDecimal::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                            if ($respuesta['valor'] == "") {
                                $registro->setValor(null);
                            } else {
                                $registro->setValor($respuesta['valor']);
                            }
                            $this->em->persist($registro);
                        }

                        break;
                    case 'TextoLargo':

                        if ($respuesta['idRegistro']) {
                            $registro = $this->em->getRepository(RegistroTextoLargo::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                            $registro->setValor($respuesta['valor']);
                            $this->em->persist($registro);
                        }

                        break;
                    case 'TextoCorto':
                        if ($respuesta['idRegistro']) {
                            $registro = $this->em->getRepository(RegistroTextoCorto::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                            $registro->setValor($respuesta['valor']);
                            $this->em->persist($registro);
                        }

                        break;
                    case 'Booleano':

                        if ($respuesta['idRegistro']) {

                            $registro = $this->em->getRepository(RegistroBooleano::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                            $registro->setValor($respuesta['valor']);
                            $this->em->persist($registro);
                        }

                        break;
                    case 'Hora':
                        if ($respuesta['idRegistro']) {

                            $registro = $this->em->getRepository(RegistroHora::class)->findOneById($respuesta['idRegistro']);
                            $registroValor = ($registro->getValor() != null) ? $registro->getValor()->format("H:i") : "";
                            if ($respuesta['valor'] != $registroValor) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registroValor;
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                            if ($respuesta['valor'] == "") {
                                $registro->setValor(null);
                            } else {
                                $registro->setValor(new \DateTime($respuesta['valor']));
                            }

                            $this->em->persist($registro);
                        }

                        break;
                    case 'Lista':

                        if ($respuesta['idRegistro']) {

                            $registro = $this->em->getRepository(RegistroLista::class)->findOneById($respuesta['idRegistro']);

                            if ($respuesta['valor'] == null) {
                                $this->em->remove($opcionForm);
                            } else {

                                $registro->setDetalleListaId($respuesta['valor']);
                                $this->em->persist($registro);
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                //cargar valor textual de la lista relacionada
                                $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($respuesta['campo_formulario_version_id']);
                                //cargar valor texto detalle lista basado en el valor y el ID de la lista
                                $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneBy(array("id" => $respuesta["valor"], "lista_id" => $campoFormularioVersion->getListaId()));

                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $detalleLista->getDescripcion();
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $detalleLista->getDescripcion();
                            }
                        } else {

                            if (isset($respuesta['valor']) && $respuesta['valor'] != null && $respuesta['valor'] != "") {
                                $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($respuesta['campo_formulario_version_id']);

                                $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneById($respuesta['valor']);

                                $nuevaLista = new RegistroLista();

                                $nuevaLista->setRegistro($registroOriginal);
                                $nuevaLista->setCampoFormularioVersion($campoFormularioVersion);
                                $nuevaLista->setDetalleLista($detalleLista);
                                $nuevaLista->setEstadoId(1);

                                $this->em->persist($nuevaLista);
                            }
                        }

                        break;
                    case 'Opcion':
                        if ($respuesta['idRegistro'] !== null) {

                            $registro = $this->em->getRepository(RegistroLista::class)->findOneById($respuesta['idRegistro']);

                            if ($respuesta['valor'] == null) {
                                $this->em->remove($opcionForm);
                            } else {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getDetalleListaId();

                                $registro->setDetalleListaId($respuesta['valor']);
                                if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                    $detalleListaNewValue = $this->em->getRepository(DetalleLista::class)->findOneById($respuesta['valor']);
                                    $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                    $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $detalleListaNewValue->getDescripcion();
                                }
                                $this->em->persist($registro);
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $detalleListaNewValue = null;
                                if ($respuesta['valor'] == null) {
                                    $detalleListaNewValue = $this->em->getRepository(DetalleLista::class)->findOneById($respuesta['valor']);
                                }
                                
                                $mostrarFront[] = array($registro->getCampoFormularioVersion()->getCampo() => $detalleListaNewValue);
                            }
                        } else if(isset($respuesta['valor'])) {

                            $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($respuesta['campo_formulario_version_id']);

                            $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneById($respuesta['valor']);

                            $nuevaLista = new RegistroLista();

                            $nuevaLista->setRegistro($registroOriginal);
                            $nuevaLista->setCampoFormularioVersion($campoFormularioVersion);
                            $nuevaLista->setDetalleLista($detalleLista);
                            $nuevaLista->setEstadoId(1);

                            $this->em->persist($nuevaLista);
                        }
                        break;
                    case 'Formulario':
                    case 'FormularioVersion':
                        if ($respuesta['idRegistro']) {
                            $registro = $this->em->getRepository(RegistroCampo::class)->findOneById($respuesta['idRegistro']);
                            if ($respuesta['valor'] != $registro->getValor()) {
                                $valores["nuevo"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta['valor'];
                                $valores["anterior"][$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $registro->getValor();

                                if ($respuesta['id'] != null) {
                                    $ids = explode("-", $respuesta['id']);
                                    $id_campo = $ids[0];
                                    $registro_origen_id = $ids[1];
                                    $registro->setIdCampo($id_campo);
                                    $registro->setRegistroIdOrigen($registro_origen_id);
                                }
                                $registro->setValor($respuesta['valor']);
                                $this->em->persist($registro);
                            }
                            if ($registro->getCampoFormularioVersion()->getMostrarFront() == "1") {
                                $mostrarFront[$registro->getCampoFormularioVersion()->getCampo()] = $respuesta["valor"];
                                $resumen[$registro->getCampoFormularioVersion()->getValorCuadroTexto()] = $respuesta["valor"];
                            }
                        }
                        break;
                    case 'Multiseleccion':
                        $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($respuesta['campo_formulario_version_id']);
                        //verifico cuales RegistrosMultiseleccion pertenecen al idRegistro
                        $registrosActuales = $this->em->getRepository(RegistroMultiseleccion::class)->findBy(array("registro_id" => $registroOriginal->getId(), "campo_formulario_version_id" => $respuesta['campo_formulario_version_id']));
                        $valorRegistrosActuales = array();
                        foreach ($registrosActuales as $registroActual) {
                            $valorRegistrosActuales[] = $registroActual->getValor();
                        }

                        $registrosActuales = $this->em->getRepository(RegistroMultiseleccion::class)->findBy(array("registro_id" => $registroOriginal->getId(), "campo_formulario_version_id" => $respuesta['campo_formulario_version_id']));
                        $registrosNuevos = $respuesta['valor'];
                        foreach ($registrosNuevos as $registroNuevo) {
                            $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneBy(["id" => $registroNuevo->{"id"}, "lista_id" => $campoFormularioVersion->getListaId()]);
                            $valorRegistrosNuevos[] = $detalleLista->getDescripcion();
                        }
                        $registrosNuevosArray = array();
                        $registrosActualesArray = array();
                        foreach ($registrosActuales as $registroActual) {
                            $registrosActualesArray[] = $registroActual->getDetalleListaId();
                        }
                        foreach ($registrosNuevos as $registroNuevo) {
                            $registrosNuevosArray[] = $registroNuevo->{"id"};
                        }
                        if (array_diff($registrosNuevosArray, $registrosActualesArray)) {
                            $valores["anterior"][$campoFormularioVersion->getValorCuadroTexto()] = $valorRegistrosActuales;
                            $valores["nuevo"][$campoFormularioVersion->getValorCuadroTexto()] = $valorRegistrosNuevos;
                        }
                        if ($campoFormularioVersion->getMostrarFront() == "1") {
                            $mostrarFront[$campoFormularioVersion->getCampo()] = $valorRegistrosNuevos;
                            $resumen[$campoFormularioVersion->getValorCuadroTexto()] = $valorRegistrosNuevos;
                        }
                        //Primero obtengo los elementos que debo agregar
                        //Debo mirar que elementos nuevos hay en registrosNuevosArray que no esten en registrosActualesArray
                        $elementosNuevos = array_diff($registrosNuevosArray, $registrosActualesArray);
                        //Luego debo saber si hay elementos para eliminar
                        $elementosBorrar = array_diff($registrosActualesArray, $registrosNuevosArray);
                        //Persisto operación
                        //Guardo los nuevos elementos
                        foreach ($elementosNuevos as $elementoNuevo) {
                            $nuevoMultiseleccion = new RegistroMultiseleccion();
                            $detalleLista = $this->em->getRepository(DetalleLista::class)->findOneById($elementoNuevo);

                            $nuevoMultiseleccion->setRegistro($registroOriginal);
                            $nuevoMultiseleccion->setCampoFormularioVersion($campoFormularioVersion);
                            $nuevoMultiseleccion->setDetalleLista($detalleLista);
                            $nuevoMultiseleccion->setEstadoId(1);

                            $this->em->persist($nuevoMultiseleccion);
                        }
                        //Elimino los elementos retirados
                        foreach ($elementosBorrar as $elementoBorrar) {
                            $registroMultiseleccion = $this->em->getRepository(RegistroMultiseleccion::class)->findOneBy(array("registro_id" => $registroOriginal->getId(), "campo_formulario_version_id" => $respuesta['campo_formulario_version_id'], "detalle_lista_id" => $elementoBorrar));
                            $this->em->remove($registroMultiseleccion);
                        }
                        break;
                }
            }
            if (isset($valores["anterior"])) {
                Auditor::registerAction($this->em, $registroOriginal, $usuario, $valores["anterior"], $valores["nuevo"], "ACTUALIZACIÓN");
            }

            if ($mostrarFront != null) {
                $registroOriginal->setBusqueda($this->mergeJson($registroOriginal->getBusqueda(), $mostrarFront));
            }

            if ($resumen != null) {
                $registroOriginal->setResumen($this->mergeJson($registroOriginal->getResumen(), $resumen));
            }

            if ($ejecucionPasoId != null && $ejecucionPasoId != "null") {
                $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
                $ejecucionPaso->setFillForm(true);
                $this->em->persist($ejecucionPaso);
            }
            $this->em->flush();
            return array("response" => array("message" => "Registro actualizado!"));
        } catch (\Exception $e) {
            $message = array("return" => array("response" => $e->getMessage()));
            return $message;
        }        
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

        $valor = implode(" ", $detalleValor);

        return $valor;
    }

    private function mergeJson($updateJson, $newJson)
    {
        foreach ($newJson as $key => $val) {
            $updateJson[$key] = $val;
        }

        return $updateJson;
    }
}
