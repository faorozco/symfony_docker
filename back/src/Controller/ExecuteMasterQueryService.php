<?php

namespace App\Controller;

use App\Entity\ConsultaMaestra;
use App\Entity\Formulario;
use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ExecuteMasterQueryService
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
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function execute(Request $request)
    {
        $items_per_page = $request->attributes->get('_items_per_page');
        $queryParam = $request->query->get("query");
        $page = $request->query->get("page");
        //Se captura la petición
        $consultaMaestra = $this->em->getRepository(ConsultaMaestra::class)->findOneById($request->attributes->get("id"));
        if (isset($consultaMaestra)) {
            //Se consulta el formulario relacionado
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($consultaMaestra->getFormularioId());
            $resultado["result"]["formulario_id"] = $formulario->getId();
            //Se extraen los campos de formulario relacionados
            $camposFormulario = $formulario->getCampoFormularios();
            //Se consulta el detalle de la consulta maestra
            $detallesConsulta = json_decode($consultaMaestra->getDetalle());

            foreach ($detallesConsulta as $detalleConsulta) {
                foreach ($camposFormulario as $campoFormulario) {
                    if ($campoFormulario->getId() == $detalleConsulta->{"campo"}) {
                        $camposConsulta[] = array(
                            "campo_formulario_id" => $campoFormulario->getId(), "tipoCampo" => $campoFormulario->getTipoCampo(),
                            "condicion" => $detalleConsulta->{"condicion"},
                            "valor" => $detalleConsulta->{"valor"},
                            "operador" => $detalleConsulta->{"operador"},
                        );
                    }
                }
            }
            //Realizar consultas SQL a los registros creados con los campos de formularios relacionados
            $registros = $this->em->getRepository(Registro::class)->findFieldValues($this->em, $camposConsulta, $queryParam, $page, $items_per_page);
            //Cargar los datos de los registros relacionados.
            foreach ($registros as $registro) {
                // $registro = $this->em->getRepository(Registro::class)->findOneById($registro_id);
                if ($registro->getEstadoId() == 1) {
                    $resultado["result"]["registros"][] = array(
                        "id" => $registro->getId(),
                        "fecha_hora" => $registro->getFechaHora(),
                        "autor" => $registro->getUsuario()->getNombre1() . " " . $registro->getUsuario()->getNombre2() . " " . $registro->getUsuario()->getApellido1() . " " . $registro->getUsuario()->getApellido2(),
                        "resumen" => json_decode($registro->getResumen(), true),
                    );
                }
            }
            if (count($registros) > 0) {
                return $resultado;
            } else {
                return array("response" => "Consulta no devolvio resultados");
            }
        } else {
            return array("response" => "No hay condiciones para evaluar");
        }
    }
}
