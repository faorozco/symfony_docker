<?php

namespace App\Repository;

use App\Entity\FormularioVersion;
use App\Entity\Usuario;
use App\Entity\TablaRetencion;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;

class FormularioVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormularioVersion::class);
    }

    public function getByUser2($query, $usuario, $em): array
    {
        $formulariosRelacionados = array();
        //traigo los grupo relacionados con el usuario autenticado
        $grupos = $usuario->getGrupos();
        foreach ($grupos as $key => $grupo) {
            $formularios = $grupo->getFormularios();
            $currentDate = new DateTime();
            $listForm = $formularios->getValues();
            for($i = 0; $i < sizeof($listForm); $i++) {
                $formulario = $listForm[$i];
                $estructuraDocumentalId = $formulario->getEstructuraDocumentalId();
                if (null !== $estructuraDocumentalId) {
                    $trd = $em->getRepository(TablaRetencion::class)->findOneBy(array("estructura_documental_id" => $estructuraDocumentalId));
                    if (isset($trd)) {
                        if ($formulario->getTipoFormulario() == 4 && $formulario->getEstadoId() == 1) {
                            if (null === $formulario->getFinVigencia()) {
                                $finVigencia = $currentDate;
                            } else {
                                $finVigencia = $formulario->getFinVigencia();
                            }
                            if ($currentDate >= $formulario->getInicioVigencia() && $currentDate <= $finVigencia) {
                                $formName = $formulario->getNombre();
                                if ($query != "") {
                                    if (strpos(strtolower($formulario->getNombre()), strtolower($query)) !== false) {
                                        $formulariosRelacionados[] = array("id" => $formulario->getId(), "nombre" => $formName);
                                    }
                                } else if ($query == "") {
                                    $formulariosRelacionados[] = array("id" => $formulario->getId(), "nombre" => $formName);
                                }
                            }
                        }
                    }
                }
            }
        }

        return (array_map("unserialize", array_unique(array_map("serialize", $formulariosRelacionados))));
    }

    public function getByUser(Usuario $usuario, $em, $query, $isSearch = false): array
    {

        if ($isSearch) {
            $filterFormActivate = "";
            $filterCurdate = "";
        } else {
            $filterFormActivate = "AND f.estado_id = 1";
            $filterCurdate = "AND CURDATE() BETWEEN FV.inicio_vigencia 
            AND CASE WHEN FV.fin_vigencia IS NULL THEN CURDATE() ELSE FV.fin_vigencia END";
        }
        $sql = "SELECT FV.id, FV.nombre FROM formulario_version FV
        INNER JOIN (
            SELECT FV.formulario_id, MAX(FV.version) AS version FROM formulario_version FV
            INNER JOIN estructura_documental_version ED ON FV.estructura_documental_version_id = ED.id
            INNER JOIN formulario f ON f.id = FV.formulario_id
            INNER JOIN formulario_grupo FG ON f.id = FG.formulario_id
            INNER JOIN usuario_grupo UG ON FG.grupo_id = UG.grupo_id
            INNER JOIN usuario U ON UG.usuario_id = U.id
            WHERE U.id = ? AND FV.tipo_formulario = 4 AND FV.estado_id = 1 " .  $filterFormActivate . " " . $filterCurdate . "             
            GROUP BY FV.formulario_id
        ) FV2 
        ON FV.formulario_id = FV2.formulario_id 
        AND FV.version = FV2.version AND FV.nombre LIKE ? ORDER BY FV.nombre ASC;";

                
        $stmt = $em->getConnection()->prepare($sql);
        
        $args = [
            $usuario->getId(),
            "%". $query . "%"
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function getFormulariosPorEstructuraDocumental($em, $estructuraDocumentalVersionId) {
        $sql = "SELECT id FROM estructura_documental_version EDV
                WHERE estado_id = 1 
                AND estructura_documental_id = (SELECT estructura_documental_id FROM estructura_documental_version EDV WHERE id = " . $estructuraDocumentalVersionId . ");";

                
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        $ids = $stmt->fetchAll();

        $formularios = new ArrayCollection();

        if(count($ids) > 0) {
            $formularios = $this->createQueryBuilder('FV')
            ->select('FV')
            ->where('FV.estado_id = 1')
            ->andWhere('FV.estructuraDocumentalVersion IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
        }

        return $formularios;
    }

    public function getFormulariosRelacionados($formularioId)
    {
        //Primero debo consultar con que formularios esta relacionado este formulario.
        //Esto se hace a través de sus campos
        //Consulto los campos del formulario

    }
}
