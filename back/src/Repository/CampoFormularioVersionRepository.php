<?php

namespace App\Repository;

use App\Entity\CampoFormularioVersion;
use App\Entity\Entidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;

class CampoFormularioVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampoFormularioVersion::class);
    }

    public function EntityListerByIds($em, $page, $query, $items_per_page, $id, $idsEntidad): array
    {
        //Primero consulto el campoFormularioVersion a consultar
        // $campoFormularioVersion = $em->getRepository(CampoFormularioVersion::class)->FindOneBy(array("id" => $id));
        //Ahora consulto los campos a imprimir de la entidad
        $entidad = $em->getRepository(Entidad::class)->FindOneBy(array("id" => $id));
        $result = array();
        if (null !== $entidad) {
            $resultado = [];
            $camposVisualizar = explode("+", $entidad->getCampoVisualizar());
            $cantidadCampos = count(explode("+", $entidad->getCampoVisualizar()));
            if ($cantidadCampos > 1) {
                $camposSelect = "e.id as id, CONCAT(" . str_replace("-", "e.", str_replace("+", ",' ', e.", $entidad->getCampoVisualizar())) . ") as descripcion";
                $camposFiltro = str_replace("-", "e.", str_replace("+", ' like :query OR e.', $entidad->getCampoBusqueda())) . " like :query";
            } else {
                $camposSelect = "e.id as id, " . str_replace("-", "e.", $entidad->getCampoVisualizar()) . " as descripcion";
                $camposFiltro = str_replace("-", "e.", str_replace("+", ' like :query OR e.', $entidad->getCampoBusqueda())) . " like :query";
            }
            $queryBuilder = $em->createQueryBuilder();

            $entityResults = $queryBuilder
                ->select($camposSelect)
                ->from('App\\Entity\\' . $entidad->getNombre(), 'e')
                ->where($camposFiltro)
                ->andWhere('e.estado_id = :estado_id')
                ->andWhere('e.id in (:idsEntidad)')
                ->setParameter('query', "%" . $query . "%")
                ->setParameter('estado_id', 1)
                ->setParameter('idsEntidad', $idsEntidad)
                ->orderBy('e.' . str_replace("-", "", $camposVisualizar[0]), "ASC")
                // ->setFirstResult(($page - 1) * $items_per_page)
                // ->setMaxResults($items_per_page)
                ->getQuery()
                ->execute();

            foreach ($entityResults as $entityResult) {
                $result[] = array(
                    "id" => $entityResult["id"],
                    "valor" => trim($entityResult["descripcion"]),
                );
            }
        } else {
            $result[] = array(
                "id" => "-1",
                "descripcion" => "No hay valores relacionados al campo",
            );
        }
        return $result;
    }

    public function EntityListerOld($em, $page, $query, $items_per_page, $id): array
    {
        //Primero consulto el campoFormularioVersion a crear
        $campoFormulario = $em->getRepository(CampoFormularioVersion::class)->FindOneBy(array("id" => $id));
        //Ahora consulto los campos a imprimir de la entidad
        $entidad = $em->getRepository(Entidad::class)->FindOneBy(array("id" => $campoFormulario->getEntidadId()));
        $result = array();
        if (null !== $entidad) {
            $resultado = [];
            $camposVisualizar = explode("+", $entidad->getCampoVisualizar());
            $cantidadCampos = count(explode("+", $entidad->getCampoVisualizar()));
            if ($cantidadCampos > 1) {
                $camposSelect = "e.id as id, CONCAT(" . str_replace("-", "e.", str_replace("+", ",' ', e.", $entidad->getCampoVisualizar())) . ") as descripcion, CONCAT(" . str_replace("-", "e.", str_replace("+", ",' ', e.", $entidad->getCampoVisualizar())) . ") as valor";
                $camposFiltro = str_replace("-", "e.", str_replace("+", ' like :query OR e.', $entidad->getCampoBusqueda())) . " like :query";
            } else {
                $camposSelect = "e.id as id, " . str_replace("-", "e.", $entidad->getCampoVisualizar()) . " as descripcion, " . str_replace("-", "e.", $entidad->getCampoVisualizar()) . " as valor";
                $camposFiltro = str_replace("-", "e.", str_replace("+", ' like :query OR e.', $entidad->getCampoBusqueda())) . " like :query";
            }
            $queryBuilder = $em->createQueryBuilder();

            $entityResults = $queryBuilder
                ->select($camposSelect)
                ->from('App\\Entity\\' . $entidad->getNombre(), 'e')
                ->where($camposFiltro)
                ->andWhere('e.estado_id = :estado_id')
                ->setParameter('query', "%" . $query . "%")
                ->setParameter('estado_id', 1)
                ->orderBy('e.' . str_replace("-", "", $camposVisualizar[0]), "ASC")
                ->setFirstResult(($page - 1) * $items_per_page)
                ->setMaxResults($items_per_page)
                ->getQuery()
                ->execute();

            foreach ($entityResults as $entityResult) {
                $result[] = array(
                    "id" => $entityResult["id"],
                    "descripcion" => trim($entityResult["descripcion"]),
                );
            }
        } else {
            $result[] = array(
                "id" => "-1",
                "descripcion" => "No hay valores relacionados al campo",
            );
        }
        $resultado[] = $result;
        return $resultado;
    }

    public function EntityLister($em, $page, $query, $items_per_page, $id): array
    {
        //Primero consulto el campoFormularioVersion a crear
        $campoFormularioVersion = $em->getRepository(CampoFormularioVersion::class)->FindOneBy(array("id" => $id));

        if ($campoFormularioVersion->getEntidadColumnName() != null) {
            //Ahora consulto los campos a imprimir de la entidad
            $entidad = $em->getRepository(Entidad::class)->FindOneBy(array("id" => $campoFormularioVersion->getEntidadId()));
            $result = array();
            if (null !== $entidad) {
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
                    $camposFiltro = "e." . implode(" like :query OR e.", $columnOrder) . " like :query";
                } else {
                    $camposSelect = "e.id as id, e." . $entidadColumnName . " as descripcion, e." . $entidadColumnName . " as valor";
                    $camposFiltro = "e." . implode(" like :query OR e.", $columnOrder) . " like :query";
                }

                $resultado = [];
                $queryBuilder = $em->createQueryBuilder();

                $entityResults = $queryBuilder
                    ->select($camposSelect)
                    ->from('App\\Entity\\' . $entidad->getNombre(), 'e')
                    ->where($camposFiltro)
                    ->andWhere('e.estado_id = :estado_id')
                    ->setParameter('query', "%" . $query . "%")
                    ->setParameter('estado_id', 1)
                    ->orderBy('e.' . str_replace("-", "", $entidadColumnName), "ASC")
                    ->setFirstResult(($page - 1) * $items_per_page)
                    ->setMaxResults($items_per_page)
                    ->getQuery()
                    ->execute();

                foreach ($entityResults as $entityResult) {
                    $result[] = array(
                        "id" => $entityResult["id"],
                        "descripcion" => trim($entityResult["descripcion"]),
                    );
                }
            } else {
                $result[] = array(
                    "id" => "-1",
                    "descripcion" => "No hay valores relacionados al campo",
                );
            }
            $resultado[] = $result;
            return $resultado;
        } else {
            return $this->EntityListerOld($em, $page, $query, $items_per_page, $id);
        }
    }

    public function FormFieldLoader($formularioVersionId)
    {
        $camposFormularioVersion = $this->createQueryBuilder("campoFormularioVersion")
            ->where('campoFormularioVersion.formulario_version_id  = :formulario_version_id')
            ->andWhere('campoFormularioVersion.oculto_al_radicar = :oculto_al_radicar')
            ->andWhere('campoFormularioVersion.estado_id = :estado_id')
            ->setParameter('formulario_version_id', $formularioVersionId)
            ->setParameter('oculto_al_radicar', 0)
            ->setParameter('estado_id', 1)
            ->addOrderBy('campoFormularioVersion.posicion', 'ASC')
            ->getQuery()
            ->execute();

        return $camposFormularioVersion;
    }

    public function FormFieldLoaderByFlow($em, $formularioVersionId, $ejecucionPasoId)
    {
        $eventos = $this->getFieldsByEventConfig($em, $ejecucionPasoId);

        $query = $this->createQueryBuilder("campoFormularioVersion")
            ->where('campoFormularioVersion.formulario_version_id  = :formulario_version_id')
            //->andWhere('campoFormularioVersion.oculto_al_radicar = :oculto_al_radicar')
            ->andWhere('campoFormularioVersion.estado_id = :estado_id');

        if(count($eventos) > 0) {
            $jsonConfig = json_decode($eventos[0]["config"]);
            $campos = $jsonConfig->{"campos"};

            $query->andWhere('campoFormularioVersion.campo_formulario_id IN (:campos)')
            ->setParameter('campos', $campos);
        }

        $camposFormularioVersion = $query->setParameter('formulario_version_id', $formularioVersionId)
        //->setParameter('oculto_al_radicar', 0)
        ->setParameter('estado_id', 1)
        ->addOrderBy('campoFormularioVersion.posicion', 'ASC')
        ->getQuery()
        ->execute();

        return $camposFormularioVersion;
    }

    public function getFieldsByEventConfig($em, $ejecucionPasoId) {
        $sql = "SELECT pev.config FROM ejecucion_paso ep
        INNER JOIN paso_version pv ON ep.paso_version_id = pv.id
        INNER JOIN paso_evento_version pev ON pev.paso_version_id = pv.id
        WHERE ep.id = ? AND pev.father_id = 3";

        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $ejecucionPasoId
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function FormFieldLister($em, $page, $query, $items_per_page, $campoFormularioId): array
    {
        // Saber que tipo de Registro_Entidad se debe consultar
        // Primero consulto el campoFormularioVersion para saber que tipo de registro_[campo_formulario.tipo_campo] es
        $campoFormularioVersion = $em->getRepository(CampoFormularioVersion::class)->FindOneBy(array("id" => $campoFormularioId));
        $campoFormularioRelacionado = $em->getRepository(CampoFormularioVersion::class)->FindOneBy(array("id" => $campoFormularioVersion->getCampoFormularioVersionId()));

        // Luego recuperar todos los valores relacionados en esa
        // entidad que hagan referencia al id del tipo de campo solicitado
        // Campos a consultar:
        // registro_texto_largo registro_texto_corto registro_numerico_moneda
        // registro_numerico_entero registro_numerico_decimal registro_hora registro_fecha
        //ordenamiento Ok
        if (null !== $campoFormularioRelacionado) {
            $queryBuilder = $em->createQueryBuilder();
            if ($campoFormularioRelacionado->getTipoCampo() == "Entidad") {            
                //Filtrar por esos resultIds
                $queryBuilderResult = $em->createQueryBuilder();
                $result = $queryBuilderResult
                    ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
                    ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 'r')
                    ->innerJoin('r.campoFormularioVersion', 'cfv', Expr\Join::WITH, 'cfv.id = r.campo_formulario_version_id and cfv.campo_formulario_id = :campo_formulario_id')
                    //->andWhere("r.campo_formulario_version_id = :campo_formulario_version_id")
                    ->where("r.valor like :query")
                    ->andWhere("r.estado_id = :estado_id")
                    ->andWhere("r.valor IS NOT NULL AND r.valor <> ''")
                    //->andWhere("r.id_entidad in (:resultIds)")
                    ->setParameter('campo_formulario_id', $campoFormularioRelacionado->getCampoFormularioId())
                    ->setParameter('estado_id', 1)
                    //->setParameter('resultIds', $ids)
                    ->setParameter('query', "%" . $query . "%")
                    ->orderBy('r.valor', "ASC")
                    ->setFirstResult(($page - 1) * $items_per_page)
                    ->setMaxResults($items_per_page)
                    ->getQuery()
                    ->execute();
            } else {
                if ($query != "") {
                    $result = $queryBuilder
                        ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
                        ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 'r')
                        ->where("r.valor like :query")
                        ->andWhere("r.campo_formulario_version_id = :campo_formulario_version_id")
                        ->andWhere("r.estado_id = :estado_id")
                        ->andWhere("r.valor IS NOT NULL")
                        ->setParameter('query', "%" . $query . "%")
                        ->setParameter('campo_formulario_version_id', $campoFormularioVersion->getCampoFormularioVersionId())
                        ->setParameter('estado_id', 1)
                        ->orderBy('r.valor', "ASC")
                        ->setFirstResult(($page - 1) * $items_per_page)
                        ->setMaxResults($items_per_page)
                        ->getQuery()
                        ->execute();
                } else {
                    $result = $queryBuilder
                        ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
                        ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 'r')
                        ->where("r.campo_formulario_version_id = :campo_formulario_version_id")
                        ->andWhere("r.estado_id = :estado_id")
                        ->andWhere("r.valor IS NOT NULL")
                        ->setParameter('campo_formulario_version_id', $campoFormularioVersion->getCampoFormularioVersionId())
                        ->setParameter('estado_id', 1)
                        ->orderBy('r.valor', "ASC")
                        ->setFirstResult(($page - 1) * $items_per_page)
                        ->setMaxResults($items_per_page)
                        ->getQuery()
                        ->execute();
                }
            }

            return $result;
        } else {
            return array("response" => array("mensaje" => "Este no es un campo tipo Formulario"));
        }

        //retornar el arreglo de datos relacionados
    }

    public function findBasicFields($formulario_version_id, $page, $query, $order_by)
    {
        $special_fields = array("Multiseleccion");
        foreach ($order_by as $key => $value) {
            $sort = $value;
            $sort_attr = $key;
        }

        $campos = $this->createQueryBuilder("campoFormularioVersion")
            ->where('campoFormularioVersion.tipo_campo NOT IN (:special_fields)')
            ->andWhere('campoFormularioVersion.formulario_version_id  = :formulario_version_id')
            ->andWhere('campoFormularioVersion.campo  like :query')
            ->setParameter('special_fields', $special_fields)
            ->setParameter('formulario_version_id', $formulario_version_id)
            ->setParameter('query', "%" . $query . "%")
            ->select('campoFormularioVersion.id as id, campoFormularioVersion.campo as campo, campoFormularioVersion.valor_cuadro_texto as valorCuadroTexto')
            ->addOrderBy('campoFormularioVersion.' . $sort_attr, $sort)
            ->getQuery()
            ->getResult();

        return $campos;
    }

    public function findFormulariosRelacionados($em, $formularioVersionId, $query)
    {
        $sql = "SELECT fv.id as id, fv.nombre as nombre  FROM formulario_version fv 
            INNER JOIN (
                SELECT fv.formulario_id, MAX(fv.version) as version FROM formulario_version fv
                INNER JOIN campo_formulario_version cfv ON cfv.formulario_version_id = fv.id AND cfv.campo_formulario_version_id IN (SELECT cfv.id FROM campo_formulario_version cfv
                INNER JOIN formulario_version fv ON cfv.formulario_version_id = fv.id
                INNER JOIN formulario_version fv2 ON fv.formulario_id = fv2.formulario_id
                WHERE cfv.indice = 1 AND fv2.id = ?)  GROUP BY fv.formulario_id
            ) fv2
            ON fv.formulario_id = fv2.formulario_id AND fv.version = fv2.version AND  fv.nombre LIKE ?";

                
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $formularioVersionId,
            "%" . $query . "%"
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function findOneByCampoFormularioId($camposFormularioId)
    {
        $result = $this->createQueryBuilder("cv")
            ->where('cv.campo_formulario_id = :campos_formulario_id')
            ->setParameter('campos_formulario_id', $camposFormularioId)
            ->addOrderBy('cv.formulario_version_id', "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function findAllVersionByFormularioVersion($em, $formularioVersionId)
    {
        $sql = "SELECT cfv.id FROM campo_formulario_version cfv
                INNER JOIN formulario_version fv
                INNER JOIN formulario f ON f.id = fv.formulario_id
                INNER JOIN formulario_version fv2 ON fv2.formulario_id = f.id
                WHERE fv.id = ?  AND fv2.version >= fv.version  AND fv2.id = cfv.formulario_version_id";

                
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $formularioVersionId
        ];
        $stmt->execute($args);

        $ids = $stmt->fetchAll();

        $result = new ArrayCollection();

        if (count($ids) > 0) {
            $result = $this->createQueryBuilder("cfv")
            ->where('cfv.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
        }

        return $result;
    }

    public function getByEjecucionFlujoAndCampoFormulario($em, $ejecucionFlujoId, $campoFormularioId)
    {
        $sql = "SELECT cfv.id as id FROM campo_formulario_version cfv
            INNER JOIN ejecucion_flujo ef ON ef.id = ? 
            INNER JOIN registro r ON r.radicado = ef.radicado
            INNER JOIN flujo_trabajo_version ftv ON cfv.formulario_version_id = r.formulario_version_id
            WHERE ftv.id = ef.flujo_trabajo_version_id AND cfv.campo_formulario_id = ?";

                
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $ejecucionFlujoId,
            $campoFormularioId
        ];
        $stmt->execute($args);

        $ids = $stmt->fetchAll();

        if (count($ids) > 0) {
            return $ids[0];
        }

        return null;
    }
}
