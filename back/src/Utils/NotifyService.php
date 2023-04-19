<?php

namespace App\Utils;

use App\Entity\Notificacion;
use App\Entity\Notificado;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class NotifyService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($entityManager)
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
    public function notify()
    {
        $pendientesNotificar = $this->em->getRepository(Notificacion::class)->findToNotify();
        foreach ($pendientesNotificar as $pendienteNotificar) {
            //obtengo el listado de usuarios a notificar
            $destinatarios = json_decode($pendienteNotificar->getPara());
            if (null !== $destinatarios) {
                foreach ($destinatarios as $destinatario) {
                    $usuario = $this->em->getRepository(Usuario::class)->findOneById($destinatario);
                    $notificado = new Notificado();
                    $notificado->setNotificacion($pendienteNotificar);
                    $notificado->setUsuario($usuario);
                    $notificado->setEstadoId(1);                 
                    $this->em->persist($notificado);
                }
            }
            $pendienteNotificar->setNotificado(true);
        }
        $this->em->flush();
    }
}
