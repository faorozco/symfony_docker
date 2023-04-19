<?php

namespace App\Controller;

use App\Entity\Entidad;
use App\Entity\Auditoria;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Undocumented class
 */
class ListarAuditoriaService
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
     * @return Usuario
     */
    function list($items_per_page, $request)
    {
        // $order_key = array_keys($order);
        // $order_orientation = $order[$order_key[0]];
        $resultado = [];

        $entidad = $this->em->getRepository(Entidad::class)
            ->findOneBy(["nombre" => $request->query->get('entidad')]);
        if($entidad!=null){
            $resultado = $this->em->getRepository(Auditoria::class)
            ->getAuditoria($items_per_page, $request, $entidad);
        }

        return $resultado;
    }
}
