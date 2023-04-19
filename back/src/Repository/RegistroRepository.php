<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Registro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class RegistroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registro::class);
    }

    public function findMax()
    {
        $registro = $this->createQueryBuilder("r")
            ->where('r.radicacion_year IS NOT NULL')
            ->andWhere('r.radicacion_counter IS NOT NULL')
            ->addOrderBy('r.radicacion_year', 'DESC')
            ->addOrderBy('r.radicacion_counter', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $registro;
    }

    public function findFieldValues($em, $formularioId = null, $camposConsulta = null, $queryParam = null, $page, $items_per_page, $tipo_correspondencia = null, $consecutivo_correspondencia = null) //: Paginator

    {
        $filtro = "(";
        $i = 0;
        $response = array();
        if (null !== $camposConsulta) {
            //Organizo los parametros de búsqueda y sus operadores conectores
            foreach ($camposConsulta as $campoConsulta) {
                //organizar filtro con los campos a consultar
                if ($i == 0) {
                    $filtro .= "JSON_UNQUOTE(LOWER(JSON_EXTRACT(busqueda, '$." . $campoConsulta["campo_formulario"] . "'))) " . $campoConsulta["condicion"] . " LOWER('" . $campoConsulta["valor"] . "')";
                    $i = 1;
                } else if ($i != 0) {
                    $filtro .= " " . $campoConsulta["operador"] . " JSON_UNQUOTE(LOWER(JSON_EXTRACT(busqueda, '$." . $campoConsulta["campo_formulario"] . "'))) " . $campoConsulta["condicion"] . " LOWER('" . $campoConsulta["valor"] . "')";
                }
            }
            $filtro .= ")";
        }
        if (isset($formularioId)) {
            $filtro .= " AND formulario_version_id=" . $formularioId;
        }
        if (null !== $tipo_correspondencia) {
            if ($filtro == "(") {
                $filtro .= "tipo_correspondencia=" . $tipo_correspondencia;
            } else {
                $filtro .= " AND tipo_correspondencia=" . $tipo_correspondencia;
            }
        }
        if (null !== $consecutivo_correspondencia) {
            if ($filtro == "(" || null === $tipo_correspondencia) {
                $filtro .= "consecutivo=" . $consecutivo_correspondencia;
            } else {
                $filtro .= " AND consecutivo=" . $consecutivo_correspondencia;
            }
        }
        if (null === $camposConsulta && !isset($formularioId)) {
            $filtro .= ")";
        }

        $conn = $em->getConnection();
        $sql = 'SELECT  id, formulario_version_id as formularioId,usuario_id as usuarioId, radicacion_year as radicacionYear,radicacion_counter as radicacionCounter, fecha_hora as fechaHora, fecha_sticker as fechaSticker, resumen, estado_id as estadoId  FROM registro r
        WHERE ' . $filtro . '
        ORDER BY r.id ASC';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // returns an array of arrays (i.e. a raw data set)
        $results = $stmt->fetchAll();
        foreach ($results as $result) {
            $result["resumen"] = json_decode($result["resumen"], true);
            $response[] = $result;
        }
        return $response;
    }

    public function findFieldValuesByMasterQuery($em, $formularioId = null, $camposConsulta = null, $camposEstaticosConsulta = null, $queryParam = null, $page, $items_per_page, $tipo_correspondencia = null, $consecutivo_correspondencia = null, $exportData = false) //: Paginator

    {
        $filtro = "";
        $i = 0;
        $response = array();
        if (null !== $camposConsulta) {
            $filtro = "(";
            //Organizo los parametros de búsqueda y sus operadores conectores
            foreach ($camposConsulta as $campoConsulta) {
                 //organizar filtro con los campos a consultar
                 if ($i == 0) {
                    $filtro .= "JSON_UNQUOTE(LOWER(JSON_EXTRACT(busqueda, '$." . $campoConsulta["campo_formulario"] . "'))) " . $campoConsulta["condicion"] . " LOWER('" . $campoConsulta["valor"] . "')";
                    $i = 1;
                } else if ($i != 0) {
                    $filtro .= " " . $campoConsulta["operador"] . " JSON_UNQUOTE(LOWER(JSON_EXTRACT(busqueda, '$." . $campoConsulta["campo_formulario"] . "'))) " . $campoConsulta["condicion"] . " LOWER('" . $campoConsulta["valor"] . "')";
                }
            }
            $filtro .= ")";
        }

        if (null !== $tipo_correspondencia) {
            if ($filtro == "") {
                $filtro .= "r.tipo_correspondencia=" . $tipo_correspondencia;
            } else {
                $filtro .= " AND r.tipo_correspondencia=" . $tipo_correspondencia;
            }
        }
        if (null !== $consecutivo_correspondencia) {
            if ($filtro == "") {
                $filtro .= "r.consecutivo=" . $consecutivo_correspondencia;
            } else {
                $filtro .= " AND r.consecutivo=" . $consecutivo_correspondencia;
            }
        }

        if(isset($queryParam) && strlen($queryParam) > 0) {
            $filtro .= " AND LOWER(r.busqueda) LIKE LOWER(?)";
        }

        $filtroCamposEstaticos = "";
        if (null !== $camposEstaticosConsulta) {
            //Organizo los parametros de búsqueda y sus operadores conectores
            foreach ($camposEstaticosConsulta as $campoEstaticoConsulta) {
                if (strlen($filtroCamposEstaticos) == 0 && $filtro == "") {
                    $operador = "";
                } else {
                    $operador = (strlen($campoEstaticoConsulta["operador"]) > 0) ? $campoEstaticoConsulta["operador"] : "AND";
                }

                switch ($campoEstaticoConsulta["campo"]) {
                    case "radicado":
                        $campoEstatico = "r.radicado";
                        break;
                    case "fecha_hora":
                        $campoEstatico = "r.fecha_hora";
                        break;
                    case "usuario_id":
                        $campoEstatico = "u.login";
                        break;
                    case "sede":
                        $campoEstatico = "r.sede";
                        break;
                }
                
                $filtroCamposEstaticos .= " " . $operador . " " . $campoEstatico . " " . $campoEstaticoConsulta["condicion"] . " LOWER('" . $campoEstaticoConsulta["valor"] . "')";
            }
        }

        $conn = $em->getConnection();

        $orderByVersion = ($exportData) ? 'fv.version ASC, r.fecha_hora DESC' : 'r.fecha_hora DESC';
        $showData = (!$exportData) ? ',u.nombre1 as autor, r.nombre_formulario as nombreFormulario' : '';
        $sql = 'SELECT  r.id, formulario_version_id as formularioId,usuario_id as usuarioId, radicacion_year as radicacionYear,radicacion_counter as radicacionCounter, fecha_hora as fechaHora, fecha_sticker as fechaSticker, resumen, r.estado_id as estadoId, fv.version as version, r.sede as sede, r.radicado as radicado, tc.nombre as correspondencia, r.consecutivo as consecutivo' . $showData . ' FROM registro r
        INNER JOIN formulario_version fv ON fv.formulario_id = ' . $formularioId . ' AND fv.id = r.formulario_version_id 
        INNER JOIN tipo_correspondencia tc ON tc.id = r.tipo_correspondencia 
        INNER JOIN usuario u ON u.id = r.usuario_id 
        WHERE ' . $filtro . ' ' . $filtroCamposEstaticos . '  
        ORDER BY ' . $orderByVersion;
        $stmt = $conn->prepare($sql);
        $args = [
            '%' . $queryParam . '%'
        ];
        $stmt->execute($args);
        // returns an array of arrays (i.e. a raw data set)
        $results = $stmt->fetchAll();
        foreach ($results as $result) {
            $result["resumen"] = json_decode($result["resumen"], true);
            $response[] = $result;
        }
        return $response;
    }

    public function findRegistrosByEstructuraDocumentalVersion($em, $formulario_version_id, $page, $query, $orderBy, $itemsPerPage)
    {
        $query = $this->createQueryBuilder("registro")
            ->select("registro.id, registro.formulario_version_id as formularioId, usuario.nombre1 as autor, registro.resumen, registro.fecha_hora as fecha_hora")
            ->leftJoin("registro.usuario", "usuario")
            ->where('registro.formulario_version_id = :formulario_version_id')
            ->andWhere("registro.busqueda like :query")
            // ->orWhere("usuario.nombre1 like :query OR usuario.nombre2 like :query OR usuario.apellido1 like :query OR usuario.apellido2 like :query")
            ->setParameter('formulario_version_id', $formulario_version_id)
            ->setParameter('query', "%" . $query . "%")
            ->orderBy('registro.id', $orderBy)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery();
        // ->execute();
        $doctrinePaginator = new DoctrinePaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findRegistrosByUser($em, $queryString, $page, $orderBy, $itemsPerPage, $user)
    {

        $query = $this->createQueryBuilder("registro");
        $query->select("registro.id, registro.fecha_hora as fechaHora, usuario.nombre1 as autor, registro.resumen, registro.formulario_version_id as formularioId, formularioVersion.nombre as nombreFormulario");
        $query->leftJoin("registro.usuario", "usuario");
        $query->leftJoin("registro.formularioVersion", "formularioVersion");
        $query->where('registro.usuario_id = :usuario_id');
        $query->andWhere("LOWER(registro.busqueda) like LOWER(:query)");
        $query->andWhere("registro.estado_id = :estado_id");
        $query->setParameter('usuario_id', $user->getId());
        $query->setParameter('query', "%" . $queryString . "%");
        $query->setParameter('estado_id', 1);
        $query->orderBy('registrofecha_hora.', "DESC");
        if (null !== $orderBy) {
            $query->orderBy('registro.' . key($orderBy), $orderBy[key($orderBy)]);
        }
        $query->setFirstResult(($page - 1) * $itemsPerPage);
        $query->setMaxResults($itemsPerPage);
        $query->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findRegistrosByFormularioVersionId($formulariosId, $page, $query, $orderBy, $itemsPerPage)
    {
        $query = $this->createQueryBuilder("registro")
            ->select("registro.id, registro.formulario_version_id as formularioId, usuario.nombre1 as autor, registro.resumen, registro.fecha_hora as fecha_hora")
            ->leftJoin("registro.usuario", "usuario")
            ->where('registro.formulario_version_id IN (:formulariosId)')
            ->andWhere("registro.busqueda like :query")
            // ->orWhere("usuario.nombre1 like :query OR usuario.nombre2 like :query OR usuario.apellido1 like :query OR usuario.apellido2 like :query")
            ->setParameter('formulariosId', $formulariosId)
            ->setParameter('query', "%" . $query . "%")
            ->orderBy('registro.id', $orderBy)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery();
        // ->execute();
        $doctrinePaginator = new DoctrinePaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findIndexValue($em, $value, $campoFormularioVersionId, $fieldName) {
        $sql = "SELECT r.id as registroId, r.resumen->\"$.\"\"" . $fieldName . "\"\"\" as valor FROM registro r
        INNER JOIN formulario_version fv ON r.formulario_version_id = fv.id
        INNER JOIN formulario f ON fv.formulario_id = f.id
        INNER JOIN campo_formulario cf ON cf.formulario_id = f.id
        INNER JOIN campo_formulario_version cfv ON cfv.campo_formulario_id = cf.id AND cfv.indice = 1
        WHERE cfv.id = ? AND r.resumen->\"$.\"\"" . $fieldName . "\"\"\" = ?;";

            
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $campoFormularioVersionId,
            $value
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function findIndexValueInEntity($em, $value, $campoFormularioVersionId, $fieldName) {
        $sql = "SELECT r.id as registroId, r.resumen->\"$." . $fieldName . "\" as valor FROM registro r
        INNER JOIN formulario_version fv ON r.formulario_version_id = fv.id
        INNER JOIN formulario f ON fv.formulario_id = f.id
        INNER JOIN campo_formulario cf ON cf.formulario_id = f.id
        INNER JOIN campo_formulario_version cfv ON cfv.campo_formulario_id = cf.id AND cfv.indice = 1
        WHERE cfv.id = ? AND r.resumen->\"$." . $fieldName . "\" = ?;";

            
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $campoFormularioVersionId,
            $value
        ];
        $stmt->execute($args);

        return $stmt->fetchAll();
    }
}
