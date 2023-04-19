<?php

namespace App\Controller;

use App\Entity\CampoFormulario;
use App\Entity\Formulario;
use App\Entity\TipoCorrespondencia;
use App\Entity\Tercero;
use App\Utils\ArrayExport;
use App\Utils\TextUtils;
use App\Utils\ZipManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class GenerarEstructuraModeloService
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
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function generate(Request $request)
    {
        $fecha = new DateTime();
        $resultado = array();
        $content = array();
        $camposFormularioActivos = array();
        //$file_name = $fileToUpload->getClientOriginalName();

        $formulario_id = $request->attributes->get('id');
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($formulario_id);

        //Crear nombre archivo
        $usuario = $this->tokenStorage->getToken()->getUser();
        $fileName = TextUtils::slugify($formulario->getNombre()) . "_" . $usuario->getLogin() . "_" . $fecha->format('YmdHis');
        $importedFile = "files_" . $fileName . ".zip";

        //recibir los parametros de la petición:
        $fileToUpload = $request->files->get("archivo");
        //almacenar en el directorio tmp del sistema
        //Leer contenido archivo .zip
        if (isset($fileToUpload)) {
            $fileToUpload->move($_ENV['TMP_LOCATION'], $importedFile);
            $content = ZipManager::ReadContent($_ENV['TMP_LOCATION'] . $importedFile);
        }

        if (isset($content["hasSubfolder"]) && $content["hasSubfolder"] == true) {
            $resultado[$fileName] = array("response" => array("respuesta" => "Archivo .zip no puede tener subcarpetas"));
        } else {
            //recordar crear una tarea programada que borre archivos temporales almacenados en /back/tmp
            $camposFormulario = $formulario->getCampoFormularios();
            if (count($camposFormulario) == 0) {
                $resultado[$fileName] = array("response" => array("respuesta" => "Formulario no tiene campos a exportar"));
            } else {
                //verificar campos activos
                foreach ($camposFormulario as $campoFormulario) {
                    if ($campoFormulario->getEstadoId() == 1 && $campoFormulario->getOcultoAlRadicar() != 1) {
                        $camposFormularioActivos[] = $campoFormulario;
                    }
                }

                $cantidadCampos = count($camposFormularioActivos);

                //Diseño del arreglo con los campos del formulario
                $resultado[$fileName] = $this->GenerateFormatForm($camposFormularioActivos, $content, $cantidadCampos);

                //Construcción de archivos arreglos con datos auxiliares
                //Dependiendo de los campos del formulario

                $resultado = $this->GenerateSpecialFieldsFiles($camposFormularioActivos, $resultado);

                $resultado = $this->GenerateTipoCorrespondencia($resultado);
            }
        }
        $exportedFile = "export_" . $fileName . ".zip";
        $exportData = new ArrayExport($resultado, $exportedFile);
        $response = $exportData->Export($request);
        return $response;
    }

    protected function GenerateTipoCorrespondencia($resultado)
    {
        //consultar la entidad Tipo de Correspondencia
        $tiposCorrespondencia = $this->em->getRepository(TipoCorrespondencia::class)->findAll();
        foreach ($tiposCorrespondencia as $tipoCorrespondencia) {
            $tipos[] = array("id" => $tipoCorrespondencia->getId(), "nombre" => $tipoCorrespondencia->getNombre());
        }
        $resultado["tipos-correspondencia"] = $tipos;
        return $resultado;
    }
    protected function GenerateFormatForm($camposFormulario, $content, $cantidadCampos)
    {
        foreach ($camposFormulario as $campoFormulario) {
            $resultado[0][] = $campoFormulario->getCampo();
        }
        $resultado[0][$cantidadCampos] = "tipo_correspondencia";
        if (isset($content) && count($content) > 0) {
            $i = 1;

            $resultado[0][$cantidadCampos + 1] = "Archivos";
            $resultado[0][$cantidadCampos + 2] = "Fecha Archivo";
            foreach ($content as $item) {
                for ($j = 0; $j < $cantidadCampos; $j++) {
                    $resultado[$i][$j] = "";
                }
                $resultado[$i][$cantidadCampos] = " ";
                $resultado[$i][$cantidadCampos + 1] = $item;
                $resultado[$i][$cantidadCampos + 2] = " ";
                $i++;
            }
        }
        return $resultado;
    }

    protected function GenerateSpecialFieldsFiles($camposFormulario, $resultado)
    {
        $file = array();
        foreach ($camposFormulario as $campoFormulario) {
            switch ($campoFormulario->getTipoCampo()) {
                case "Lista":
                    //Traer los detalles de la lista
                    $lista = $campoFormulario->getLista()->getDetalleListas();
                    foreach ($lista as $detallelista) {
                        $resultado[TextUtils::slugify($campoFormulario->getTipoCampo() . "_" . $campoFormulario->getCampo())][] = array(
                            "id" => $detallelista->getId(),
                            "codigo" => $detallelista->getCodigo(),
                            "descripcion" => $detallelista->getDescripcion(),
                        );
                    }
                    break;
                case "Entidad":
                    $entidad = $campoFormulario->getEntidad()->getNombre();
                    $campos = str_replace("-", "", $campoFormulario->getEntidad()->getCampoVisualizar());
                    if ($entidad == "Tercero") {
                        $campos = "identificacion+nombre";
                    }
                    $camposVisualizar = explode("+", $campos);
                    $fields = array();
                    foreach ($camposVisualizar as $campoVisualizar) {
                        $fields[] = "e." . $campoVisualizar;
                    }
                    $fieldToSearch = implode(", ", $fields);
                    $queryBuilder = $this->em->createQueryBuilder();
                    $resultEntidad = $queryBuilder
                        ->select("e.id, " . $fieldToSearch)
                        ->from('App\\Entity\\' . $entidad, 'e')
                        ->where('e.estado_id=:estado_id')
                        ->setParameter('estado_id', 1)
                        ->getQuery()
                        ->execute();
                    $resultado[TextUtils::slugify($campoFormulario->getTipoCampo() . "_" . $entidad . "_campo_" . $campoFormulario->getCampo())] = $resultEntidad;
                    break;
                case "Formulario":
                    //Saber que campo formulario tiene relacionado
                    $campoFormularioId = $campoFormulario->getCampoFormularioId();
                    //Saber que campo formulario es
                    $campoFormulario = $this->em->getRepository(CampoFormulario::class)->findOneById($campoFormularioId);
                    $entidad = $campoFormulario->getEntidad();
                    //Saber que tipo de Campo es
                    $tipoCampo = $campoFormulario->getTipoCampo();

                    //Verificar que valores están relacionados con ese tipo de Campo
                    $queryBuilder = $this->em->createQueryBuilder();
                    $camposFormularioRelacionados = $queryBuilder
                        ->select("r.id, r.valor, r.id_entidad")
                        ->from('App\\Entity\\Registro' . $tipoCampo, 'r')
                        ->where('r.campo_formulario_id = :campoformularioid')
                        ->setParameter('campoformularioid', $campoFormulario->getId())
                        ->getQuery()
                        ->execute();
                    if ($entidad->getNombre() == "Tercero") {
                        foreach ($camposFormularioRelacionados as $campoFormularioRelacionado) {
                            $tercero = $this->em->getRepository(Tercero::class)->findOneById($campoFormularioRelacionado["id_entidad"]);
                            $resultado[TextUtils::slugify($campoFormulario->getTipoCampo() . "_" . $campoFormulario->getCampo())][] = array(
                                "id" => $campoFormularioRelacionado["id"],
                                "valor" => $campoFormularioRelacionado["valor"],
                                "identificacion" => $tercero->getIdentificacion(),
                            );
                        }
                    } else {
                        foreach ($camposFormularioRelacionados as $campoFormularioRelacionado) {
                            $resultado[TextUtils::slugify($campoFormulario->getTipoCampo() . "_" . $campoFormulario->getCampo())][] = array(
                                "id" => $campoFormularioRelacionado["id"],
                                "valor" => $campoFormularioRelacionado["valor"],
                            );
                        }
                    }

                    //Consultar que tipo de campo es
                    //Realizar la consulta de los datos relacionados con ese registro
                    break;
                case "Multiseleccion":
                    $lista = $campoFormulario->getLista()->getDetalleListas();
                    foreach ($lista as $detallelista) {
                        $resultado[TextUtils::slugify($campoFormulario->getTipoCampo() . "_" . $campoFormulario->getCampo())][] = array(
                            "id" => $detallelista->getId(),
                            "codigo" => $detallelista->getCodigo(),
                            "descripcion" => $detallelista->getDescripcion(),
                        );
                    }
                    break;
            }
        }
        return $resultado;
    }
}
