<?php

namespace App\Controller;

use App\Entity\Entidad;
use App\Entity\Registro;
use App\Entity\RegistroCampo;
use App\Entity\CampoFormularioVersion;
use App\Entity\RegistroMultiseleccion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;

/**
 * Undocumented class
 */
class CamposFormularioRespuestaService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }

    public function Get($idFormulario, $registroId, $ejecucionPasoId)
    {

        if ($ejecucionPasoId != null) {
            $eventos = $this->em->getRepository(CampoFormularioVersion::class)->getFieldsByEventConfig($this->em, $ejecucionPasoId);
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->select("campo,
                    rmul.detalle_lista_id as valorMUL,
                    rdec.valor as valorDEC,
                    rent.valor as valorENT,
                    rmon.valor as valorMON,
                    rcor.valor as valorCOR,
                    rlar.valor as valorLAR,
                    rend.id_entidad,
                    rfec.valor as valorFEC,
                    rhor.valor as valorHOR,
                    rlis.detalle_lista_id as valorLIS,
                    rfor.valor as valorFOR,
                    rbol.valor as valorBOL,
                    rmul.id as idMUL,
                    rdec.id as idDEC,
                    rent.id as idENT,
                    rmon.id as idMON,
                    rcor.id as idCOR,
                    rlar.id as idLAR,
                    rend.id as idEND,
                    rfec.id as idFEC,
                    rhor.id as idHOR,
                    rlis.id as idLIS,
                    rfor.id as idFOR,
                    rbol.id as idBOL
                    ")
            ->from('App\\Entity\\CampoFormularioVersion', 'campo')
            ->leftJoin('campo.registroMultiseleccions', 'rmul', Expr\Join::WITH, 'rmul.campo_formulario_version_id = campo.id and rmul.registro_id = ' . $registroId)
            ->leftJoin('campo.registroNumericoDecimals', 'rdec', Expr\Join::WITH, 'rdec.campo_formulario_version_id = campo.id and rdec.registro_id = ' . $registroId)
            ->leftJoin('campo.registroNumericoEnteros', 'rent', Expr\Join::WITH, 'rent.campo_formulario_version_id = campo.id and rent.registro_id = ' . $registroId)
            ->leftJoin('campo.registroNumericoMonedas', 'rmon', Expr\Join::WITH, 'rmon.campo_formulario_version_id = campo.id and rmon.registro_id = ' . $registroId)
            ->leftJoin('campo.registroTextoCortos', 'rcor', Expr\Join::WITH, 'rcor.campo_formulario_version_id = campo.id and rcor.registro_id = ' . $registroId)
            ->leftJoin('campo.registroTextoLargos', 'rlar', Expr\Join::WITH, 'rlar.campo_formulario_version_id = campo.id and rlar.registro_id = ' . $registroId)
            ->leftJoin('campo.registroEntidads', 'rend', Expr\Join::WITH, 'rend.campo_formulario_version_id = campo.id and rend.registro_id = ' . $registroId)
            ->leftJoin('campo.registroFechas', 'rfec', Expr\Join::WITH, 'rfec.campo_formulario_version_id = campo.id and rfec.registro_id = ' . $registroId)
            ->leftJoin('campo.registroHoras', 'rhor', Expr\Join::WITH, 'rhor.campo_formulario_version_id = campo.id and rhor.registro_id = ' . $registroId)
            ->leftJoin('campo.registroListas', 'rlis', Expr\Join::WITH, 'rlis.campo_formulario_version_id = campo.id and rlis.registro_id = ' . $registroId)
            ->leftJoin('campo.registroCampos', 'rfor', Expr\Join::WITH, 'rfor.campo_formulario_version_id = campo.id and rfor.registro_id = ' . $registroId)
            ->leftJoin('campo.registroBooleanos', 'rbol', Expr\Join::WITH, 'rbol.campo_formulario_version_id = campo.id and rbol.registro_id = ' . $registroId)
            ->where("campo.formulario_version_id = :id")
            ->andWhere("campo.estado_id = :estadoId");

        if(isset($eventos) && count($eventos) > 0) {
            $jsonConfig = json_decode($eventos[0]["config"]);
            $campos = $jsonConfig->{"campos"};

            $query = $query->andWhere('campo.campo_formulario_id IN (:campos)')
            ->setParameter('campos', $campos);
        }

        $entityResults = $query->setParameter('id', $idFormulario)
            ->setParameter('estadoId', 1)
            ->orderBy('campo.posicion', 'ASC')
            ->getQuery()
            ->execute();

        $datos = array();

        foreach ($entityResults as $value) {
            $campoFormularioVersion = $value[0];
            switch ($campoFormularioVersion->getTipoCampo()) {

                case 'Fecha':
                    $loadValue = ($value['valorFEC'] != null)? $value['valorFEC'] : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idFEC'], "valor" => $loadValue));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'Entidad':
                    $loadValue = ($value['id_entidad'] != null)? $value['id_entidad'] : "";
                    //Reinicio de $camposResult
                    $camposResult=array();
                    //Buscar campo de busqueda
                    $entidad = $this->em->getRepository(Entidad::class)->findOneBy(array("nombre" => $value[0]->getEntidad()->getNombre()));

                    if ($campoFormularioVersion->getEntidadColumnName() == null) {
                        $camposVisualizar = explode("+", str_replace("-", "", $entidad->getCampoVisualizar()));
                    } else {
                        $config = $campoFormularioVersion->getConfig();
                        $entidadColumnName = $campoFormularioVersion->getEntidadColumnName();
                        $columnOrder = $config["entidadColumnOrder"];

                        if($campoFormularioVersion->getIndice() == true) {
                            $camposVisualizar = $columnOrder;
                        } else {
                            $camposVisualizar[] = $entidadColumnName;
                        }
                    }
                    
                    foreach ($camposVisualizar as $campoVisualizar) {
                        $camposResult[] = "e." . $campoVisualizar;
                    }
                    $camposConsulta = implode(",' | ',", $camposResult);
                    $queryBuilderEntity = $this->em->createQueryBuilder();
                    if (count($camposResult) > 1) {
                        $result = $queryBuilderEntity
                            ->select("e.id as id, concat(" . $camposConsulta . ") as nombre")
                            ->from('App\\Entity\\' . $campoFormularioVersion->getEntidad()->getNombre(), 'e')
                            ->where('e.id = :id')
                            ->setParameter('id', $value['id_entidad'])
                            ->getQuery()
                            ->execute();
                    }else if (count($camposResult) == 1) {
                        $result = $queryBuilderEntity
                        ->select("e.id as id, " . $camposConsulta . " as nombre")
                        ->from('App\\Entity\\' . $campoFormularioVersion->getEntidad()->getNombre(), 'e')
                        ->where('e.id = :id')
                        ->setParameter('id', $value['id_entidad'])
                        ->getQuery()
                        ->execute();
                    }

                    $valores = array();
                    foreach ($result as $data) {
                        $valores = array("id" => $data["id"], "descripcion" => $data["nombre"]);
                    }
                    //Averiguar con que entidad tiene relacion. Esto se puede saber por el registroId
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idEND'], "valor" => $valores));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'NumericoMoneda':
                    $loadValue = ($value['valorMON'] != null)? $value['valorMON'] : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idMON'], "valor" => $loadValue));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'NumericoEntero':
                    $loadValue = ($value['valorENT'] != null)? $value['valorENT'] : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idENT'], "valor" => $loadValue));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'NumericoDecimal':
                    $loadValue = ($value['valorDEC'] != null)? $value['valorDEC'] : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idDEC'], "valor" => $loadValue));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'Multiseleccion':
                    $loadValue = ($value['idMUL'] != null)? $value['idMUL'] : "";
                    $results = $this->em->getRepository(RegistroMultiseleccion::class)->FindBy(array("registro_id" => $registroId, "campo_formulario_version_id" => $campoFormularioVersion->getId()));

                    $valoresMulti = array();
                    foreach ($results as $result) {
                        $valoresMulti[] = array("id" => $result->getDetalleLista()->getId(), "descripcion" => $result->getDetalleLista()->getDescripcion());
                    }
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idMUL'], "valor" => $valoresMulti));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'TextoLargo':
                    if (null !== $value['valorLAR']) {
                        $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idLAR'], "valor" => $value['valorLAR']));
                        $datos[] = $campoFormularioVersion;
                    }

                    break;
                case 'TextoCorto':
                    if (null !== $value['valorCOR']) {
                        $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idCOR'], "valor" => $value['valorCOR']));
                        $datos[] = $campoFormularioVersion;
                    }

                    break;
                case 'Booleano':
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idBOL'], "valor" => $value['valorBOL']));
                    $datos[] = $campoFormularioVersion;

                    break;
                case 'Hora':
                    $loadValue = ($value['valorHOR'] != null)? $value['valorHOR']->format("H:i") : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idHOR'], "valor" => $loadValue));
                    $datos[] = $campoFormularioVersion;

                    break;
                case 'Lista':
                    $loadValue = ($value['valorLIS'] != null)? $value['valorLIS'] : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idLIS'], "valor" => $value['valorLIS']));
                    $datos[] = $campoFormularioVersion;
                    break;
                case 'Opcion':
                    $loadValue = ($value['valorLIS'] != null)? $value['valorLIS'] : "";
                    $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idLIS'], "valor" => $value['valorLIS']));
                    $datos[] = $campoFormularioVersion;

                    break;
                case 'Formulario':
                case 'FormularioVersion':
                    $loadValue = ($value['valorFOR'] != null)? $value['valorFOR'] : "";
                    if ($value['idFOR'] != null) {
                        $result = $this->em->getRepository(RegistroCampo::class)->FindOneBy(array("id" => $value['idFOR'], "registro_id" => $registroId));
                        $valor = array("id" => $result->getId(), "valor" => $result->getValor());
                        $campoFormularioVersion->setValorDefecto(array("idRegistro" => $value['idFOR'], "valor" => $valor));
                        $datos[] = $campoFormularioVersion;
                    } else {
                        $datos[] = $campoFormularioVersion;
                    }
                    
                    break;

            }

        }

        return $datos;

    }
}
