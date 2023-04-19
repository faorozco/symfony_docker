<?php

namespace App\Controller;

use App\Entity\CampoFormulario;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class EntityListerService
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
    function list(string $page, $query, $items_per_page, $id): array
    {
        // $order_key = array_keys($order);
        // $order_orientation = $order[$order_key[0]];
        $resultado = [];

        $resultado = $this->em->getRepository(CampoFormulario::class)
            ->EntityLister($this->em, $page, $query, $items_per_page, $id);

        return $resultado;
    }
}
