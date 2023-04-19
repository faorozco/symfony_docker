<?php

namespace App\Controller;

use App\Entity\TablaRetencion;
use App\Dto\TablaRetencionDto;
use Doctrine\ORM\EntityManagerInterface;

class TablaRetencionGetEspecialService
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
     * Cargar lista de tablas de retención
     *
     * @param string $login
     *
     * @return TablaRetencionDto
     */
    public function get(string $page, $query, $order, $items_per_page): array
    {
        $order_key = array_keys($order);
        $order_orientation = $order[$order_key[0]];
        $tablasRetencionDto = [];

        $tablasRetencionDto = $this->em->getRepository(TablaRetencion::class)
            ->FindSpecialGet($this->em, $page, $query, $order_key, $order_orientation, $items_per_page);

        return $tablasRetencionDto;
    }
}
