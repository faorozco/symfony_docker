<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Usuario;
use App\Entity\Grupo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\ORM\Query\Expr;

class UsuarioRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function findUsers($queryString, $page, $itemsPerPage, $estado, $orderBy)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u")
        ->where("u.login like :query")
        ->andWhere("u.estado_id = :estado_id")
        ->setParameter('estado_id', $estado)
        ->setParameter('query', "%" . $queryString . "%")
        ->setFirstResult(($page - 1) * $itemsPerPage);
        if (null !== $orderBy) {
            $query->orderBy('u.login', $orderBy);
        }
        $query->setMaxResults($itemsPerPage)
        ->getQuery();
        $doctrinePaginator = new DoctrinePaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }


    public function findUsersPost($login, $estado, $bloqueo, $sesion, $page, $pageSize,$orden)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, 
                  u.login, 
                  u.nombre1, 
                  u.nombre2, 
                  u.apellido1, 
                  u.apellido2, 
                  u.estado_id, 
                  u.bloqueo, 
                  u.activeSesion,
                  u.numero_documento,
                  u.celular,
                  u.email,
                  u.telefono_fijo_residencia,
                  u.direccion_residencia,
                  u.genero,
                  u.fecha_nacimiento,
                  u.proceso_id,
                  u.cargo_id,
                  c.nombre AS cargo_nombre,
                  p.nombre AS proceso_nombre,
                  s.nombre AS sede_nombre
                  ")
        ->innerJoin('u.cargo', 'c')
        ->innerJoin('u.proceso', 'p')
        ->innerJoin('u.sede', 's')
        ->where("u.login like :query or u.nombre1 like :query or u.nombre2 like :query or u.apellido1 like :query or u.apellido2 like :query ");
        if($estado!=""  && ((int)$estado || (int)$estado==0)){ 
            $query->andWhere("u.estado_id = :estado_id")
                  ->setParameter('estado_id',(int)$estado);
        }
        if((int)$bloqueo){ 
            $query->andWhere("u.bloqueo = :bloqueo")
                  ->setParameter('bloqueo',(int)$bloqueo);
        }
        if((int)$sesion){ 
            $query->andWhere("u.activeSesion = :activeSesion")
                  ->setParameter('activeSesion',(int)$sesion);
        }                                   
        $query->setParameter('query', "%" . $login . "%")
        ->setFirstResult(($page - 1) * $pageSize);
        if (null !== $orden) {
            $query->orderBy('u.login', $orden);
        }
        $query->setMaxResults($pageSize)
        ->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findUsersPostOnly($login, $estado, $bloqueo, $sesion, $page, $pageSize,$orden)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, 
                  u.login, 
                  u.nombre1, 
                  u.nombre2, 
                  u.apellido1, 
                  u.apellido2,
                  CONCAT(u.nombre1, ' ', u.nombre2, ' ', u.apellido1, ' ', u.apellido2) as name
                  ")
        ->where("u.login like :query or u.nombre1 like :query or u.nombre2 like :query or u.apellido1 like :query or u.apellido2 like :query ");
        if($estado!=""  && ((int)$estado || (int)$estado==0)){ 
            $query->andWhere("u.estado_id = :estado_id")
                  ->setParameter('estado_id',(int)$estado);
        }
        if((int)$bloqueo){ 
            $query->andWhere("u.bloqueo = :bloqueo")
                  ->setParameter('bloqueo',(int)$bloqueo);
        }
        if((int)$sesion){ 
            $query->andWhere("u.activeSesion = :activeSesion")
                  ->setParameter('activeSesion',(int)$sesion);
        }                                   
        $query->setParameter('query', "%" . $login . "%")
        ->setFirstResult(($page - 1) * $pageSize);
        if (null !== $orden) {
            $query->orderBy('u.login', $orden);
        }
        $query->setMaxResults($pageSize)
        ->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function findUsersEvents()
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, 
                  u.login, 
                  u.nombre1, 
                  u.nombre2, 
                  u.apellido1, 
                  u.apellido2, 
                  u.estado_id
                  ")
        ->where("u.estado_id = 1")
        ->orderBy('u.login', "ASC")
        ->getQuery();         
        return $query->execute();
    }

    public function findUsersGroup($em, $grupoId, $filter)
    {
        $sql = 'SELECT ug.usuario_id AS id FROM usuario_grupo ug WHERE ug.grupo_id = ?';
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $grupoId
        ];
        $stmt->execute($args);
        // returns an array of arrays (i.e. a raw data set)
        $results = $stmt->fetchAll();

        $query = $this->createQueryBuilder("u")
        ->select("u.id, u.login")
        ->where("u.id IN (:usuarios)")
        ->andWhere("u.estado_id = 1")
        ->andWhere("u.login like :filter")
        ->setParameter("usuarios", $results)
        ->setParameter("filter", '%'.$filter.'%')
        ->orderBy('u.login', "ASC")
        ->getQuery(); 
            
        return $query->execute();
    }

    public function findGruopsUser($em, $userId, $filter)
    {
        $sql = 'SELECT ug.grupo_id AS id , g.nombre FROM usuario_grupo ug
        INNER JOIN grupo g ON ug.grupo_id = g.id
        WHERE ug.usuario_id = ?';
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $userId
        ];
        $stmt->execute($args);
        // returns an array of arrays (i.e. a raw data set)
        $results = $stmt->fetchAll();

            
        return $results;
    }

    public function findByGroupsFormId($em, $formularioVersionId, $filter)
    {
        $sql = 'SELECT ug.usuario_id AS id FROM usuario_grupo ug
        INNER JOIN formulario_grupo fg ON ug.grupo_id = fg.grupo_id
        INNER JOIN formulario_version fv ON fg.formulario_id = fv.formulario_id
        WHERE fv.id = ?';
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $formularioVersionId
        ];
        $stmt->execute($args);
        // returns an array of arrays (i.e. a raw data set)
        $results = $stmt->fetchAll();

        $query = $this->createQueryBuilder("u")
        ->select("u.id, u.login")
        ->where("u.id IN (:usuarios)")
        ->andWhere("u.estado_id = 1")
        ->andWhere("u.login like :filter")
        ->setParameter("usuarios", $results)
        ->setParameter("filter", '%'.$filter.'%')
        ->orderBy('u.login', "ASC")
        ->getQuery(); 
            
        return $query->execute();
    }

    public function findUsersResponsibleByGroup($em, $groupId) {
        $sql = 'SELECT u.id FROM usuario u
            INNER JOIN usuario_grupo ug ON u.id = ug.usuario_id 
            WHERE ug.grupo_id = ? AND u.estado_id = 1 ORDER BY id ASC';
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $groupId
        ];
        $stmt->execute($args);
        $users = $stmt->fetchAll();
        $userList = [];
        foreach($users as $user) {
            $userList[] = $user["id"];
        }
        return $userList;
    }

    public function findUsersResponsibleByFullName($em, $fullname) {
        $sql = "SELECT u.id FROM usuario u WHERE concat(apellido1, ' ', apellido2, ' ', nombre1, ' ', nombre2) = ?";
        $stmt = $em->getConnection()->prepare($sql);
        $args = [
            $fullname
        ];
        $stmt->execute($args);
        $users = $stmt->fetchAll();
        $userList = [];
        foreach($users as $user) {
            $userList[] = $user["id"];
        }
        return $userList;
    }

    public function findUsersSystem($filter)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, u.login")
        ->andWhere("u.estado_id = 1")
        ->andWhere("u.login like :filter")
        ->setParameter("filter", '%'.$filter.'%')
        ->orderBy('u.login', "ASC")
        ->getQuery(); 
            
        return $query->execute();
    }

    public function findUsersNameSystem($filter)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, u.nombre1, u.nombre2, u.apellido1, u.apellido2")
        ->andWhere("u.estado_id = 1")
        ->andWhere("u.login like :filter OR u.nombre1 like :filter OR u.nombre2 like :filter OR u.apellido1 like :filter OR u.apellido2 like :filter")
        ->setParameter("filter", '%'.$filter.'%')
        ->orderBy('u.login', "ASC")
        ->getQuery(); 
            
        return $query->execute();
    }

    public function findOneForId($id)
    {
        $query = $this->createQueryBuilder("u")
        ->select("u.id, 
                  u.login, 
                  u.nombre1, 
                  u.nombre2, 
                  u.apellido1, 
                  u.apellido2, 
                  u.estado_id, 
                  u.bloqueo, 
                  u.activeSesion,
                  u.numero_documento,
                  u.celular,
                  u.sede_id,
                  u.email,
                  u.telefono_fijo_residencia,
                  u.direccion_residencia,
                  u.genero,
                  u.fecha_nacimiento,
                  u.proceso_id,
                  u.cargo_id,
                  c.nombre AS cargo_nombre,
                  p.nombre AS proceso_nombre,
                  s.nombre AS sede_nombre
                  ")
        ->innerJoin('u.cargo', 'c')
        ->innerJoin('u.proceso', 'p')
        ->innerJoin('u.sede', 's')
        ->where("u.id = ".$id)
        ->setMaxResults(1)
        ->getQuery();

         return $query->getArrayResult();
    }

}
