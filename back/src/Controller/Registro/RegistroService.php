<?php

namespace App\Controller\Registro;

use App\Entity\CampoFormularioVersion;
use App\Entity\Entidad;
use App\Entity\Formulario;
use App\Entity\Registro;
use App\Entity\EjecucionFlujo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class RegistroService
{
    private $_em;
    private $_result;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->result = array();

    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function cargarRegistroPorFormularioId(Request $request)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registro = $this->em->getRepository(Registro::class)->findOneById($request->attributes->get("id"));
        return $this->cargarRegistro($registro);
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function cargarRegistroPorRadicado(Request $request)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registro = $this->em->getRepository(Registro::class)->findBy(
            array(
                "radicado" => $request->attributes->get("radicado"),
            )
        );

        return $this->cargarRegistro($registro[0]);
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function existRegistro(Request $request)
    {
        $ejecucionPasoId = $request->attributes->get("id");
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registros = $this->em->getRepository(Registro::class)->findBy(
            array(
                "ejecucion_paso_id" => $ejecucionPasoId,
            )
        );

        $registroId = "";
        $formularioVersionId = "";
        if (count($registros) > 0) {
            $registroId = $registros[0]->getId();
            $formularioVersionId = $registros[0]->getFormularioVersionId();
        }

        return array("response" => array("exist" => count($registros) > 0, "registroId" => $registroId, "formularioVersionId" => $formularioVersionId));
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    private function cargarRegistro(Registro $registro)
    {
        //2. Traer el formulario relacionado
        //$formulario = $this->em->getRepository(Formulario::class)->findOneById($registro->getFormularioVersionId());
        // Por cada campo formulario verifico si hay registros relacionados con este
        // Tener presente que el campo esta relacionado con una entidadRegistro
        // Asi que se puede verificar si hay registros relacionados con cada campo
        //$camposFormularioVersion = $formulario->getCampoFormularios();
        $camposFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findBy(["formulario_version_id"=>$registro->getFormularioVersionId(), "estado_id"=>1], 
        ['posicion' => 'ASC']);
        foreach ($camposFormularioVersion as $campoFormularioVersion) {
            //3. Extraer el Tipo de Campo y Verificar en $registro si este tiene valores.
            $this->getValueByField($campoFormularioVersion, $registro);
        }
        if (isset($this->result)) {
            return $this->result;
        } else {
            return array("response" => "Registro no tiene campos relacionados");
        }
    }
    protected function traerValor($registrocampos, $campoFormularioVersion, $tipoDato)
    {
        foreach ($registrocampos as $registrocampo) {
            $valor = $registrocampo->getValor();
            switch ($tipoDato) {
                case "time":
                    $valor = ($valor == null) ? "" : $valor->format("H:i");
                    break;
                case "date":
                    $valor = ($valor == null) ? "" : $valor->format("Y-m-d");
                    break;
            }            
            if ($registrocampo->getCampoFormularioVersionId() == $campoFormularioVersion->getId()) {
                $campo = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($registrocampo->getCampoFormularioVersion()->getId());
                $this->result[] = array(
                    "etiqueta" => $campo->getValorCuadroTexto(),
                    "tipo" => $campo->getTipoCampo(),
                    "valor" => $valor,
                    "registro" => $registrocampo,
                );
            }
        }
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function cargarRegistroPorEjecucionFlujoId(Request $request)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registro = $this->em->getRepository(Registro::class)->findBy(
            array(
                "ejecucion_flujo_id" => $request->attributes->get("id"),
            )
        );

        if (count($registro) == 0) {
            return [];
        } else {
            return $this->cargarRegistro($registro[0]);
        }
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function registroEjecucionFlujo($ejecucionFlujoId)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $ejecucionFlujo = $this->em->getRepository(EjecucionFlujo::class)->findOneById($ejecucionFlujoId);
        $registro = $this->em->getRepository(Registro::class)->findBy(
            array(
                "radicado" => $ejecucionFlujo->getRadicado(),
            )
        );

        if (count($registro) == 0) {
            return [];
        } else {
            return $registro[0];
        }
    }

    public function registroRadicado($radicado)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registro = $this->em->getRepository(Registro::class)->findBy(
            array(
                "radicado" => $radicado,
            )
        );

        if (count($registro) == 0) {
            return [];
        } else {
            return array("response" => $registro[0]);
        }
    }

    public function getValueByField($campoFormularioVersion, $registro) {
        $tipoCampo = $campoFormularioVersion->getTipoCampo();
        switch ($tipoCampo) {
            case "TextoCorto":
                $registrocampos = $registro->getRegistroTextoCortos();
                $this->traerValor($registrocampos, $campoFormularioVersion, "string");
                break;
            case "TextoLargo":
                $registrocampos = $registro->getRegistroTextoLargos();
                $this->traerValor($registrocampos, $campoFormularioVersion, "string");
                break;
            case "NumericoMoneda":
                $registrocampos = $registro->getRegistroNumericoMonedas();
                $this->traerValor($registrocampos, $campoFormularioVersion, "number");
                break;
            case "NumericoDecimal":
                $registrocampos = $registro->getRegistroNumericoDecimals();
                $this->traerValor($registrocampos, $campoFormularioVersion, "number");
                break;
            case "Booleano":
                $registrocampos = $registro->getRegistroBooleanos();
                $this->traerValor($registrocampos, $campoFormularioVersion, "boolean");
                break;
            case "NumericoEntero":
                $registrocampos = $registro->getRegistroNumericoEnteros();
                $this->traerValor($registrocampos, $campoFormularioVersion, "number");
                break;
            case "Hora":
                $registrocampos = $registro->getRegistroHoras();
                $this->traerValor($registrocampos, $campoFormularioVersion, "time");
                break;
            case "Fecha":
                $registrocampos = $registro->getRegistroFechas();
                $this->traerValor($registrocampos, $campoFormularioVersion, "date");
                break;
            case "Entidad":
                $registrocampos = $registro->getRegistroEntidads();
                foreach ($registrocampos as $registrocampo) {
                    if ($registrocampo->getCampoFormularioVersionId() == $campoFormularioVersion->getId()) {

                        $entidad = $this->em->getRepository(Entidad::class)->FindOneBy(array("id" => $registrocampo->getCampoFormularioVersion()->getEntidadId()));

                        $camposVisualizar = explode("+", $entidad->getCampoVisualizar());
                        $cantidadCampos = count(explode("+", $entidad->getCampoVisualizar()));

                        if($campoFormularioVersion->getEntidadColumnName() == null) {
                            if ($cantidadCampos > 1) {
                                $camposSelect = "e.id as id, CONCAT(" . str_replace("-", "e.", str_replace("+", ",' ', e.", $entidad->getCampoVisualizar())) . ") as descripcion";
                            } else {
                                $camposSelect = "e.id as id, " . str_replace("-", "e.", $entidad->getCampoVisualizar()) . " as descripcion";
                            }
                        } else {
                            $config = $campoFormularioVersion->getConfig();
                            $entidadColumnName = $campoFormularioVersion->getEntidadColumnName();
                            $columnOrder = $config["entidadColumnOrder"];

                            if($campoFormularioVersion->getIndice() == true) {
                                $description = "";
                                if(count($columnOrder) > 1) {
                                    $description = "CONCAT(e." . str_replace("+", ",' | ', e.", implode("+", $columnOrder)) . ") as descripcion,";
                                } else {
                                    $description = "e." . $columnOrder[0] . " as descripcion,";
                                }
                                $camposSelect = "e.id as id, ". $description . " e." . $entidadColumnName . " as valor";
                            } else {
                                $camposSelect = "e.id as id, e." . $entidadColumnName . " as descripcion, e." . $entidadColumnName . " as valor";
                            }
                        }
                        $queryBuilder = $this->em->createQueryBuilder();

                        $entityResult = $queryBuilder
                            ->select($camposSelect)
                            ->from('App\\Entity\\' . $entidad->getNombre(), 'e')
                            ->where("e.id = :id_entidad")
                            ->setParameter('id_entidad', $registrocampo->getIdEntidad())
                            ->getQuery()
                            ->execute();

                        if (count($entityResult) == 0) {
                            $valor = "";
                        } else {
                            $valor = $entityResult[0]["descripcion"];
                        }

                        $this->result[] = array(
                            "etiqueta" => $registrocampo->getCampoFormularioVersion()->getValorCuadroTexto(),
                            "tipo" => $registrocampo->getCampoFormularioVersion()->getTipoCampo(),
                            "valor" => $valor,
                            "registro" => $registrocampo,
                        );
                    }
                }
                break;
            case "Formulario":
            case "FormularioVersion":
                $registrocampos = $registro->getRegistroCampos();
                foreach ($registrocampos as $registrocampo) {
                    if ($registrocampo->getCampoFormularioVersionId() == $campoFormularioVersion->getId()) {
                        $campo = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($registrocampo->getCampoFormularioVersion()->getCampoFormularioVersionId());
                        $this->result[] = array(
                            "etiqueta" => $campoFormularioVersion->getValorCuadroTexto(),
                            "tipo" => $registrocampo->getCampoFormularioVersion()->getTipoCampo(),
                            "valor" => $registrocampo->getValor(),
                            "registro" => $registrocampo,
                        );
                    }
                }
                break;
            case "Multiseleccion":
                $registrocampos = $registro->getRegistroMultiseleccions();
                $this->resultMulti = array();
                $registroArray = array();
                foreach ($registrocampos as $registrocampo) {
                    if ($registrocampo->getCampoFormularioVersionId() == $campoFormularioVersion->getId()) {
                        $campo = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($registrocampo->getCampoFormularioVersionId());
                        $this->resultMulti[] = $registrocampo->getDetalleLista()->getDescripcion();
                        $registroArray[] = $registrocampo;
                    }
                }
                if (isset($campo)) {
                    $this->result[] = array(
                        "etiqueta" => $campo->getValorCuadroTexto(),
                        "tipo" => $campo->getTipoCampo(),
                        "valor" => implode(" - ", $this->resultMulti),
                        "registro" => $registroArray,
                    );
                }
                break;
            case "Lista":
                $registrocampos = $registro->getRegistroListas();
                foreach ($registrocampos as $registrocampo) {
                    if ($registrocampo->getCampoFormularioVersionId() == $campoFormularioVersion->getId()) {
                        $campo = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($registrocampo->getCampoFormularioVersionId());
                        $this->result[] = array(
                            "etiqueta" => $campo->getValorCuadroTexto(),
                            "valor" => $registrocampo->getDetalleLista()->getDescripcion(),
                            "registro" => $registrocampo,
                        );
                    }
                }
                break;
            case "Opcion":
                $registrocampos = $registro->getRegistroListas();
                foreach ($registrocampos as $registrocampo) {
                    if ($registrocampo->getCampoFormularioVersionId() == $campoFormularioVersion->getId()) {
                        $campo = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($registrocampo->getCampoFormularioVersionId());
                        $this->result[] = array(
                            "etiqueta" => $campo->getValorCuadroTexto(),
                            "valor" => $registrocampo->getDetalleLista()->getDescripcion(),
                            "registro" => $registrocampo,

                        );
                    }
                }
                break;
        }

        return $this->result;
    }

    public function valueByFieldAndFlow($campoFormularioId, $ejecucionFlujoId) {
        $this->result = array();
        $registro = $this->registroEjecucionFlujo($ejecucionFlujoId);
        
        $campoFormularioVersionId = $this->em->getRepository(CampoFormularioVersion::class)->getByEjecucionFlujoAndCampoFormulario($this->em, $ejecucionFlujoId, $campoFormularioId);
        $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioVersionId);
        $value = null;

        if($registro != null && $campoFormularioVersion != null) {
            $value = $this->getValueByField($campoFormularioVersion, $registro);
        }

        if ($value != null && count($value) > 0) {
            $value = $value[0];
        }
        return $value;
    }

    public function valueByFieldVersionAndRegister($campoFormularioVersionId, $registro) {
        $this->result = array();
        $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioVersionId);
        $value = null;

        if($registro != null && $campoFormularioVersion != null) {
            $value = $this->getValueByField($campoFormularioVersion, $registro);
        }

        if ($value != null && count($value) > 0) {
            $value = $value[0];
        }
        return $value;
    }

    public function valueByFieldAndRegister($campoFormularioId, $registro) {
        $this->result = array();
        $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneBy(array("formulario_version_id" => $registro->getFormularioVersionId(), "campo_formulario_id" => $campoFormularioId));
        $value = null;

        if($registro != null && $campoFormularioVersion != null) {
            $value = $this->getValueByField($campoFormularioVersion, $registro);
        }

        if ($value != null && count($value) > 0) {
            $value = $value[0];
        }
        return $value;
    }

    public function registroPorId($registroId)
    {
        //1. Traer el registro a consultar
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registros = $this->em->getRepository(Registro::class)->findById($registroId);

        if (count($registros) == 0) {
            return [];
        } else {
            return array("response" => $registros[0]);
        }
    }
}
