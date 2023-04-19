<?php

namespace App\Controller;

use App\Entity\Entidad;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Undocumented class
 */
class EntidadCampoService
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
     * @return Entidad
     */
    function get(): array
    {
        $entidades = $this->em->getRepository(Entidad::class)->findBy(array("estado_id" => 1, "campo" => 1));

        return $entidades;
    }
}
