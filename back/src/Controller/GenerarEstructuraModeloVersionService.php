<?php

namespace App\Controller;

use App\Entity\CampoFormularioVersion;
use App\Entity\FormularioVersion;
use App\Entity\TipoCorrespondencia;
use App\Entity\Tercero;
use App\Utils\ArrayExport;
use App\Utils\TextUtils;
use App\Utils\ZipManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Query\Expr;

/**
 * Undocumented class
 */
class GenerarEstructuraModeloVersionService
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
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($formulario_id);

        //Crear nombre archivo
        $usuario = $this->tokenStorage->getToken()->getUser();
        $fileName = TextUtils::slugify($formularioVersion->getNombre()) . "_" . $usuario->getLogin() . "_" . $fecha->format('YmdHis');
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
            $camposFormularioVersion = $formularioVersion->getCampoFormulariosVersion();
            if (count($camposFormularioVersion) == 0) {
                $resultado[$fileName] = array("response" => array("respuesta" => "Formulario no tiene campos a exportar"));
            } else {
                //verificar campos activos
                foreach ($camposFormularioVersion as $campoFormularioVersion) {
                    if ($campoFormularioVersion->getEstadoId() == 1 && $campoFormularioVersion->getOcultoAlRadicar() != 1) {
                        $camposFormularioActivos[] = $campoFormularioVersion;
                    }
                }

                $cantidadCampos = count($camposFormularioActivos);

                //Diseño del arreglo con los campos del formularioVersion
                $resultado[$fileName] = $this->GenerateFormatForm($camposFormularioActivos, $content, $cantidadCampos);

                //Construcción de archivos arreglos con datos auxiliares
                //Dependiendo de los campos del formularioVersion

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
    protected function GenerateFormatForm($camposFormularioVersion, $content, $cantidadCampos)
    {
        foreach ($camposFormularioVersion as $campoFormularioVersion) {
            $resultado[0][] = $campoFormularioVersion->getCampo();
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

    protected function GenerateSpecialFieldsFiles($camposFormularioVersion, $resultado)
    {
        $file = array();
        foreach ($camposFormularioVersion as $campoFormularioVersion) {
            switch ($campoFormularioVersion->getTipoCampo()) {
                case "Lista":
                    //Traer los detalles de la lista
                    $lista = $campoFormularioVersion->getLista()->getDetalleListas();
                    foreach ($lista as $detallelista) {
                        $resultado[TextUtils::slugify($campoFormularioVersion->getTipoCampo() . "_" . $campoFormularioVersion->getCampo())][] = array(
                            "id" => $detallelista->getId(),
                            "codigo" => $detallelista->getCodigo(),
                            "descripcion" => $detallelista->getDescripcion(),
                        );
                    }
                    break;
                case "Entidad":
                    $entidad = $campoFormularioVersion->getEntidad()->getNombre();
                    if($campoFormularioVersion->getEntidadColumnName() == null) {
                        $campos = str_replace("-", "", $campoFormularioVersion->getEntidad()->getCampoVisualizar());
                        if ($entidad == "Tercero") {
                            $campos = "identificacion+nombre";
                        }
                        $camposVisualizar = explode("+", $campos);
                    } else {
                        if($campoFormularioVersion->getIndice() == true) {
                            $config = $campoFormularioVersion->getConfig();
                            $columnOrder = $config["entidadColumnOrder"];
                            $camposVisualizar = $columnOrder;
                        } else {
                            $camposVisualizar[] = $campoFormularioVersion->getEntidadColumnName();
                        }
                    }

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
                    $resultado[TextUtils::slugify($campoFormularioVersion->getTipoCampo() . "_" . $entidad . "_campo_" . $campoFormularioVersion->getCampo())] = $resultEntidad;
                    break;
                case "Formulario":
                case "FormularioVersion":
                    //Saber que campo formularioVersion tiene relacionado
                    $campoFormularioVersionId = $campoFormularioVersion->getCampoFormularioVersionId();
                    //Saber que campo formularioVersion es
                    $campoFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioVersionId);
                    $entidad = $campoFormularioVersion->getEntidad();
                    //Saber que tipo de Campo es
                    $tipoCampo = $campoFormularioVersion->getTipoCampo();

                    //Verificar que valores están relacionados con ese tipo de Campo
                    $queryBuilder = $this->em->createQueryBuilder();
                    $camposFormularioRelacionados = $queryBuilder
                        ->select("r.id, r.valor, r.id_entidad")
                        ->from('App\\Entity\\Registro' . $tipoCampo, 'r')
                        ->innerJoin('r.campoFormularioVersion', 'cfv', Expr\Join::WITH, 'cfv.id = r.campo_formulario_version_id')
                        ->where('cfv.campo_formulario_id = :campoformularioid')
                        ->andWhere('cfv.estado_id = 1')
                        ->setParameter('campoformularioid', $campoFormularioVersion->getCampoFormularioId())
                        ->getQuery()
                        ->execute();
                    if ($entidad->getNombre() == "Tercero") {
                        foreach ($camposFormularioRelacionados as $campoFormularioRelacionado) {
                            $tercero = $this->em->getRepository(Tercero::class)->findOneById($campoFormularioRelacionado["id_entidad"]);
                            $resultado[TextUtils::slugify($campoFormularioVersion->getTipoCampo() . "_" . $campoFormularioVersion->getCampo())][] = array(
                                "id" => $campoFormularioRelacionado["id"],
                                "valor" => $campoFormularioRelacionado["valor"],
                                "identificacion" => $tercero->getIdentificacion(),
                            );
                        }
                    } else {
                        foreach ($camposFormularioRelacionados as $campoFormularioRelacionado) {
                            $resultado[TextUtils::slugify($campoFormularioVersion->getTipoCampo() . "_" . $campoFormularioVersion->getCampo())][] = array(
                                "id" => $campoFormularioRelacionado["id"],
                                "valor" => $campoFormularioRelacionado["valor"],
                            );
                        }
                    }

                    //Consultar que tipo de campo es
                    //Realizar la consulta de los datos relacionados con ese registro
                    break;
                case "Multiseleccion":
                    $lista = $campoFormularioVersion->getLista()->getDetalleListas();
                    foreach ($lista as $detallelista) {
                        $resultado[TextUtils::slugify($campoFormularioVersion->getTipoCampo() . "_" . $campoFormularioVersion->getCampo())][] = array(
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
