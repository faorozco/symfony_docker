<?php

namespace App\Repository;

use App\Entity\CampoFormulario;
use App\Entity\Entidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CampoFormularioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampoFormulario::class);
    }

    public function EntityListerByIds($em, $page, $query, $items_per_page, $id, $idsEntidad): array
    {
        //Primero consulto el campoFormulario a consultar
        // $campoFormulario = $em->getRepository(CampoFormulario::class)->FindOneBy(array("id" => $id));
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
        //Primero consulto el campoFormulario a crear
        $campoFormulario = $em->getRepository(CampoFormulario::class)->FindOneBy(array("id" => $id));
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
        //Primero consulto el campoFormulario a crear
        $campoFormulario = $em->getRepository(CampoFormulario::class)->FindOneBy(array("id" => $id));

        if ($campoFormulario->getEntidadColumnName() != null) {
            //Ahora consulto los campos a imprimir de la entidad
            $entidad = $em->getRepository(Entidad::class)->FindOneBy(array("id" => $campoFormulario->getEntidadId()));
            $result = array();
            if (null !== $entidad) {
                $config = $campoFormulario->getConfig();
                $entidadColumnName = $campoFormulario->getEntidadColumnName();
                $columnOrder = $config["entidadColumnOrder"];

                if($campoFormulario->getIndice() == true) {
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

    public function FormFieldLoader($request)
    {

        $camposFormulario = $this->createQueryBuilder("campoFormulario")
            ->where('campoFormulario.formulario_id  = :formulario_id')
            ->andWhere('campoFormulario.oculto_al_radicar = :oculto_al_radicar')
            ->andWhere('campoFormulario.estado_id = :estado_id')
            ->setParameter('formulario_id', $request->attributes->get('id'))
            ->setParameter('oculto_al_radicar', 0)
            ->setParameter('estado_id', 1)
            ->addOrderBy('campoFormulario.posicion', 'ASC')
            ->getQuery()
            ->execute();

        return $camposFormulario;
    }

    public function FormFieldLister($em, $page, $query, $items_per_page, $campoFormularioId): array
    {
        // Saber que tipo de Registro_Entidad se debe consultar
        // Primero consulto el campoFormulario para saber que tipo de registro_[campo_formulario.tipo_campo] es
        $campoFormulario = $em->getRepository(CampoFormulario::class)->FindOneBy(array("id" => $campoFormularioId));
        $campoFormularioRelacionado = $em->getRepository(CampoFormulario::class)->FindOneBy(array("id" => $campoFormulario->getCampoFormularioId()));

        // Luego recuperar todos los valores relacionados en esa
        // entidad que hagan referencia al id del tipo de campo solicitado
        // Campos a consultar:
        // registro_texto_largo registro_texto_corto registro_numerico_moneda
        // registro_numerico_entero registro_numerico_decimal registro_hora registro_fecha
        //ordenamiento Ok
        if (null !== $campoFormularioRelacionado) {
            $queryBuilder = $em->createQueryBuilder();
            if ($campoFormularioRelacionado->getTipoCampo() == "Entidad") {
                //Buscar los ids de los valores almacenados relacionados al campo formulario relacionado
                $resultIdEntidads = $queryBuilder
                    ->select("r.id_entidad")
                    ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 'r')
                    ->where("r.campo_formulario_id = :campo_formulario_id")
                    ->andWhere("r.estado_id = :estado_id")
                    ->andWhere("r.valor IS NOT NULL")
                    ->setParameter('campo_formulario_id', $campoFormularioRelacionado->getId())
                    ->setParameter('estado_id', 1)
                    ->orderBy('r.valor', "ASC")
                    // ->setFirstResult(($page - 1) * $items_per_page)
                    // ->setMaxResults($items_per_page)
                    ->getQuery()
                    ->getArrayResult();

                foreach ($resultIdEntidads as $resultIdEntidad) {
                    $idEntidads[] = $resultIdEntidad["id_entidad"];
                }

                $resultIds = $this->EntityListerByIds($em, $page, $query, $items_per_page, $campoFormularioRelacionado->getEntidadId(), $idEntidads);
                $ids=array();
                foreach ($resultIds as $resultId) {
                    $ids[] = $resultId["id"];
                }
                //Filtrar por esos resultIds
                $queryBuilderResult = $em->createQueryBuilder();
                $result = $queryBuilderResult
                    ->select("CONCAT(r.id,'-',r.registro_id) as id, r.valor as valor")
                    ->from('App\\Entity\\Registro' . $campoFormularioRelacionado->getTipoCampo(), 'r')
                    ->andWhere("r.campo_formulario_id = :campo_formulario_id")
                    ->andWhere("r.estado_id = :estado_id")
                    ->andWhere("r.valor IS NOT NULL")
                    ->andWhere("r.id_entidad in (:resultIds)")
                    ->setParameter('campo_formulario_id', $campoFormularioRelacionado->getId())
                    ->setParameter('estado_id', 1)
                    ->setParameter('resultIds', $ids)
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
                        ->andWhere("r.campo_formulario_id = :campo_formulario_id")
                        ->andWhere("r.estado_id = :estado_id")
                        ->andWhere("r.valor IS NOT NULL")
                        ->setParameter('query', "%" . $query . "%")
                        ->setParameter('campo_formulario_id', $campoFormulario->getCampoFormularioId())
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
                        ->where("r.campo_formulario_id = :campo_formulario_id")
                        ->andWhere("r.estado_id = :estado_id")
                        ->andWhere("r.valor IS NOT NULL")
                        ->setParameter('campo_formulario_id', $campoFormulario->getCampoFormularioId())
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

    public function findBasicFields($formulario_id, $page, $query, $order_by)
    {
        $special_fields = array("Multiseleccion");
        foreach ($order_by as $key => $value) {
            $sort = $value;
            $sort_attr = $key;
        }

        $campos = $this->createQueryBuilder("campoFormulario")
            ->where('campoFormulario.tipo_campo NOT IN (:special_fields)')
            ->andWhere('campoFormulario.formulario_id  = :formulario_id')
            ->andWhere('campoFormulario.campo  like :query')
            ->setParameter('special_fields', $special_fields)
            ->setParameter('formulario_id', $formulario_id)
            ->setParameter('query', "%" . $query . "%")
            ->select('campoFormulario.id as id, campoFormulario.campo as campo, campoFormulario.valor_cuadro_texto as valorCuadroTexto')
            ->addOrderBy('campoFormulario.' . $sort_attr, $sort)
            ->getQuery()
            ->getResult();

        return $campos;
    }

    public function findFormulariosRelacionados($camposFormularioIds, $query)
    {
        $result = $this->createQueryBuilder("campoFormulario")
            ->leftJoin('campoFormulario.formulario', 'formulario')
            ->where('campoFormulario.campo_formulario_id IN (:campos_formulario_ids)')
            ->andWhere('formulario.nombre like :query')
            ->setParameter('campos_formulario_ids', $camposFormularioIds)
            ->setParameter('query', "%" . $query . "%")
            ->select('formulario.id as id, formulario.nombre')
            ->addOrderBy('formulario.nombre', "ASC")
            ->getQuery()
            ->getResult();

        return $result;
    }
}
