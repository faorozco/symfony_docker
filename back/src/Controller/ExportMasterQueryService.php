<?php

namespace App\Controller;

use App\Entity\ConsultaMaestra;
use App\Entity\Formulario;
use App\Entity\Registro;
use App\Utils\DataExport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ExportMasterQueryService
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
    public function export(Request $request)
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
            $exportData = new DataExport($registros);
            return $exportData->Export();
        } else {
            return array("response" => "Consulta no retorno resultados");
        }
    }
}
