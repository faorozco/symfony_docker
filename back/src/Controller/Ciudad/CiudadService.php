<?php

namespace App\Controller\Ciudad;

use App\Entity\Ciudad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class CiudadService
{
    private $_em;
    private $_result;

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
     * Create Tercero function
     *
     * @param string $request
     *
     * @return Tercero
     */
    public function list($filter, $order, $mostrarInactivos)
    {
        $ciudades = $this->em->getRepository(Ciudad::class)->listar($filter, $order, $mostrarInactivos);

        if (count($ciudades) > 0) {
            return $ciudades;
        } else {
            return array("response" => "No se encontraron resultados");
        }   
    }

    public function listPage($filter, $order, $mostrarInactivos, $page, $size)
    {
        $ciudades = $this->em->getRepository(Ciudad::class)->listarPage($filter, $order, $mostrarInactivos, $page, $size);

        return $ciudades;  
    }
}
