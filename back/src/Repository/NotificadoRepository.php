<?php

namespace App\Repository;

use App\Entity\Notificado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class NotificadoRepository extends ServiceEntityRepository
{
    private $tokenStorage;
    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;

        parent::__construct($registry, Notificado::class);
    }

    public function findNotificadosByRegistro($registro_id, $page, $pageSize)
    {
        $query = $this->createQueryBuilder("n")
            ->leftJoin('n.notificacion', 'notificacion')
            ->addSelect('notificacion')
            ->leftJoin('n.usuario', 'usuario')
            ->addSelect('usuario')
            ->where('notificacion.registro_id = :registro_id')
            ->setParameter('registro_id', $registro_id)
            ->select('n.id as id, notificacion.cuando as fecha, notificacion.contenido as contenidonotificacion, CONCAT(usuario.nombre1,\' \',usuario.nombre2,\' \',usuario.apellido1,\' \',usuario.apellido2) as para, n.visto, n.enviado')
            ->addOrderBy('n.id', 'DESC')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery();

        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);
        $result = new Paginator($doctrinePaginator);
        return $result;
    }

    public function getNotifiedNotificacions($items_per_page, $page, $estado_id, $order_by, $estadoTarea, $tipoFormulario, $visto, $queryString)
    {
        //extrae llave[valor] del array order_by
        //$sort = $value : ordenamiento asc o desc
        //sort_attr = $key: columna por la que se va a ordenar
        foreach ($order_by as $key => $value) {
            $sort = $value;
            $sort_attr = $key;
        }

        //traer remitente de un documento
        $usuario = $this->tokenStorage->getToken()->getUser();
        $query = $this->createQueryBuilder("notificado");
        $query->select('registro.id as registro_id, formularioVersion.id as formulario_id,
                            formularioVersion.tipo_formulario as tipo_formulario,
                            notificacion.id as notificacion_id, notificado.id as notificado_id,
                            usuario.id as remitente_id, notificado.enviado as fecha_notificacion,
                            notificado.visto as visto, notificacion.contenido as contenido_notificacion,
                            notificado.enviado fecha_enviado, CONCAT(usuario.nombre1,\' \', 
                            usuario.nombre2,\' \',usuario.apellido1,\' \', usuario.apellido2) as remitente, notificado.comentario as comentario')
            ->leftJoin('notificado.notificacion', 'notificacion')
            ->leftJoin('notificacion.registro', 'registro')
            ->leftJoin('registro.usuario', 'usuario')
            ->leftJoin('registro.formularioVersion', 'formularioVersion');
        $query->where('notificado.usuario_id = :usuario_id');
        $query->andWhere('notificacion.contenido like :queryString OR usuario.nombre1 like :queryString OR usuario.nombre2 like :queryString OR usuario.apellido1 like :queryString OR usuario.apellido2 like :queryString');
        $query->orWhere('notificado.usuario_id = :usuario_id AND notificado.comentario = true');
        
        //Si se envió el tipoFormulario verificar qué tipo de formulario se esta consultando
        if (isset($tipoFormulario)) {
            switch ($tipoFormulario) {
                //Formulario del sistema
                case "1":break;
                //Formulario tarea abierta
                case "2":
                    $query->leftJoin('formularioVersion.campoFormulariosVersion', 'campoFormulariosVersion')
                        ->leftJoin('registro.registroListas', 'registroListas')
                        ->andWhere("campoFormulariosVersion.id = :estadoTareaField")
                        ->andWhere("registroListas.detalle_lista_id = :estadoTarea")
                        ->setParameter('estadoTareaField', 3) //El 3 es el identificador del campo relacionado con el formulario Tareas
                        ->setParameter('estadoTarea', $estadoTarea);
                    break;
                //Formulario tarea flujo de trabajo
                case "3":break;
                case "4":break;
                    //Formulario normal
            }

        }
        //Verificar el estado de visualización de las notificaciones
        if (isset($visto)) {
            if ("false" === $visto) {
                $query->andWhere("notificado.visto IS NULL");
            } else if ("true" === $visto) {
                $query->andWhere("notificado.visto IS NOT NULL");
            }
        }
        $resultado = $query->andWhere('notificado.estado_id = :estado_id')
            ->setParameter('usuario_id', $usuario->getId())
            ->setParameter('estado_id', $estado_id)
            ->setParameter('queryString', "%" . $queryString . "%")
            ->addOrderBy('notificado.' . $sort_attr, $sort)            
            ->addOrderBy('comentario', 'DESC')
            ->getQuery()
            ->getResult();

        return $resultado;
    }

    public function NotifyComment($sentNotifications)
    {
        $result = $this->createQueryBuilder('n')
            ->update()
            ->set('n.comentario', true)
            ->where('n.notificacion_id IN (:sentNotifications)')
            ->setParameter('sentNotifications', $sentNotifications)
            ->getQuery()
            ->execute();        
    }
}
