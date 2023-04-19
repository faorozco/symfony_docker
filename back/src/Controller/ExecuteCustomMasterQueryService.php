<?php

namespace App\Controller;

use App\Entity\CampoFormularioVersion;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ExecuteCustomMasterQueryService
{
    private $_em;
    private $hashFieldOrders = null;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function execute(Request $request)
    {
        $data = json_decode($request->getContent());
        //Verificar si viene parametro de tipo_correspondencia
        $tipo_correspondencia = null;
        if (isset($data->{"tipo_correspondencia"})) {
            $tipo_correspondencia = $data->{"tipo_correspondencia"};
        }

        //Verificar si viene parametro de consecutivo
        $consecutivo_correspondencia = null;
        if (isset($data->{"consecutivo_correspondencia"})) {
            $consecutivo_correspondencia = $data->{"consecutivo_correspondencia"};
        }
        //Se captura la petición
        $items_per_page = $request->attributes->get('_items_per_page');
        $page = $data->{"page"};
        
        $queryParam = null;
        if (isset($data->{"query"})) {
            $queryParam = $data->{"query"};
        }
        $formularioId=null;
        if (isset($data->{"formulario_id"})) {
            $formularioId = $data->{"formulario_id"};
        }
        
        $camposConsulta=null;
        $camposEstaticosConsulta=null;
        if (isset($data) || null !== $consecutivo_correspondencia || null !== $tipo_correspondencia) {
            //Se consulta el detalle de la consulta maestra
            if (isset($data->{"detalle_consulta"})) {
                $detallesConsulta = $data->{"detalle_consulta"};

                foreach ($detallesConsulta as $detalleConsulta) {
                    if($detalleConsulta->{"valor"}==" "){
                        $detalleConsulta->{"valor"}="";
                    }
                    switch ($detalleConsulta->{"condicion"}) {
                    case "igual":
                        $condicion = "=";
                        if (is_numeric($detalleConsulta->{"valor"})) {
                            $valor = $detalleConsulta->{"valor"};
                        } else {
                            $valor = $detalleConsulta->{"valor"} ;
                        }
                        break;
                    case "diferente":
                        $condicion = "<>";
                        if (is_numeric($detalleConsulta->{"valor"})) {
                            $valor = $detalleConsulta->{"valor"};
                        } else {
                            $valor = $detalleConsulta->{"valor"};
                        }
                        break;
                    case "contiene":
                        $condicion = "like";
                        $valor = "%" . $detalleConsulta->{"valor"} . "%";
                        break;
                    case "no contiene":
                        $condicion = "not like";
                        $valor = "%" . $detalleConsulta->{"valor"} . "%";
                        break;
                    case "comienza por":
                        $condicion = "like";
                        $valor = $detalleConsulta->{"valor"} . "%";
                    case "termina en":
                        $condicion = "like";
                        $valor = "%" . $detalleConsulta->{"valor"} . "%";
                        break;
                        break;
                    case "mayor que":
                        $condicion = ">";
                        $valor = $detalleConsulta->{"valor"};
                        break;
                    case "menor que":
                        $condicion = "<";
                        $valor = $detalleConsulta->{"valor"};
                        break;
                    case "mayor o igual que":
                        $condicion = ">=";
                        $valor = $detalleConsulta->{"valor"};
                        break;
                    case "menor o igual que":
                        $condicion = "<=";
                        $valor = $detalleConsulta->{"valor"};
                        break;
                    }
                    switch ($detalleConsulta->{"operador"}) {
                    case "Y":
                        $conector = "AND";
                        break;
                    case "O":
                        $conector = "OR";
                        break;
                    case " ":
                        $conector = "";
                        break;
                    }

                    if (is_numeric($detalleConsulta->{"idCampo"})) {
                        $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($detalleConsulta->{"idCampo"});
                        $camposConsulta[] = array(
                            //"campo_formulario_id" => $campoFormulario->getId(),
                            "campo_formulario" => $campoFormularioVersion->getCampo(),
                            //"tipoCampo" => $campoFormulario->getTipoCampo(),
                            "condicion" => $condicion,
                            "valor" => $valor,
                            "operador" => $conector
                        );
                    } else {
                        switch ($detalleConsulta->{"idCampo"}) {
                            case "radicado":
                                $campoEstatico = "radicado";
                                break;
                            case "radicadoFecha":
                                $campoEstatico = "fecha_hora";
                                break;
                            case "usuario":
                                $campoEstatico = "usuario_id";
                                break;
                            case "sede":
                                $campoEstatico = "sede";
                                break;
                        }

                        $camposEstaticosConsulta[] = array(
                            //"campo_formulario_id" => $campoFormulario->getId(),
                            "campo" => $campoEstatico,
                            //"tipoCampo" => $campoFormulario->getTipoCampo(),
                            "condicion" => $condicion,
                            "valor" => $valor,
                            "operador" => $conector
                        );
                    }
                    

                    
                    
                }
            }

            if (!isset($data->{"consultaMaestraId"})){
                $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($formularioId);
                $formularioId = $formularioVersion->getFormularioId();
            }

            //Realizar consultas SQL a los registros creados con los campos de formularios relacionados
            $registros = $this->em->getRepository(Registro::class)->findFieldValuesByMasterQuery($this->em, $formularioId, $camposConsulta, $camposEstaticosConsulta, $queryParam, $page, $items_per_page, $tipo_correspondencia, $consecutivo_correspondencia);

            if ($this->hashFieldOrders == null) $this->hashFieldOrders = array();

            for ($i = 0, $n = count($registros); $i < $n; $i++) {
                $registros[$i]["resumen"] = $this->orderValuesField($registros[$i]["formularioId"], $registros[$i]["resumen"]);
            }

            return $registros;
        } else {
            return array("response" => "No hay condiciones para evaluar");
        }
    }

    private function orderValuesField(String $formularioVersionId, Array $values) {
        $orderArray = array();

        if (isset($this->hashFieldOrders[$formularioVersionId])) {
            $fields = $this->hashFieldOrders[$formularioVersionId];
        } else {
            $fields = $this->em->getRepository(CampoFormularioVersion::class)->findBy(["formulario_version_id" => $formularioVersionId], ['posicion' => 'ASC']);
            $this->hashFieldOrders[$formularioVersionId] = $fields;
        }

        foreach($fields as $field) {
            //if (isset($values[$field->getValorCuadroTexto()]) && (is_array($values[$field->getValorCuadroTexto()]) || trim($values[$field->getValorCuadroTexto()]) != "")) {
            if (isset($values[$field->getValorCuadroTexto()])) {
                $orderArray[$field->getValorCuadroTexto()] = $values[$field->getValorCuadroTexto()];
            }
        }
        
        return $orderArray;
    }
}
