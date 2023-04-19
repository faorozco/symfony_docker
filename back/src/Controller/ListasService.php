<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Lista;

/**
 * Undocumented class
 */
class ListasService
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
    public function get(Request $request)
    {
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $pageSize = $request->query->get('pageSize');
        $orden = $request->query->get('orden');
        $estado = $request->query->get('estado');
        $listas = $this->em->getRepository(Lista::class)->findListas($query, $page, $pageSize, $estado, $orden);

        if (isset($listas)) {
            return $listas;
        } else {
            return array("response" => "No se encontraron listas");
        }
    }
}
