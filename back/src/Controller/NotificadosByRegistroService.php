<?php

namespace App\Controller;

use App\Entity\Notificado;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\StickerGenerator;


/**
 * Undocumented class
 */
class NotificadosByRegistroService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
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
    public function Get(Request $request)
    {
        $registroId = $request->attributes->get("id");
        $page = $request->query->get("page");
        $pageSize = $request->query->get("pageSize");

        $notificados = $this->em->getRepository(Notificado::class)->findNotificadosByRegistro($registroId, $page, $pageSize);        
        if (isset($notificados)) {
            return $notificados;           
        }  else {
            return array("response" => "Registro no tiene notificados relacionados");
        }
    }
}
