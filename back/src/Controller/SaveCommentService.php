<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Notificacion;
use App\Entity\Notificado;
use App\Entity\Registro;
use App\Entity\Usuario;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class SaveCommentService
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
    public function save(Request $request)
    {
        //Guardado de la entidad
        $data = json_decode($request->getContent());
        $dataUsuario = explode("/", $data->{"usuario"});
        $dataRegistro = explode("/", $data->{"registro"});
        $usuarioRepository = $this->em->getRepository(Usuario::class);
        
        
        $registroRepository = $this->em->getRepository(Registro::class);
        $comentario = new Comentario();
        $comentario->setUsuario($usuarioRepository->findOneById($dataUsuario[3]));
        $comentario->setDetalle($data->{"detalle"});
        $comentario->setEstadoId($data->{"estadoId"});
        $comentario->setRegistro($registroRepository->findOneById($dataRegistro[3]));
        $comentario->setFecha(new DateTime($data->{"fecha"}));
        
        //Se Cargan las entidades involucradas en el proceso de Notificacion(Notificacion y Notificado)
        $notificacionRepository = $this->em->getRepository(Notificacion::class);
        $notificadoRepository = $this->em->getRepository(Notificado::class);
        //Se obtienen los ids de las notificaciones que fueron enviadas al registro
        $sentNotifications = $notificacionRepository->getNotificationsByRegistro($dataRegistro[3]);        
        //Se actualiza el campo comentario de las Notificaciones enviadas(Notificados)
        $notificadoRepository->NotifyComment($sentNotifications);
        //Se guarda el comentario
        $this->em->persist($comentario);
        return $comentario;

    }
}
