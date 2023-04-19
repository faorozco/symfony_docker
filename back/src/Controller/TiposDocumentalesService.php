<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class TiposDocumentalesService
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
    public function get(Request $request)
    {
        $id = $request->attributes->get("id");
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $items_per_page = $request->attributes->get('_items_per_page');
        //Obtener los registros donde el codigo_directorio = 0 y el type sea igual a tipo_documental
        return $this->em->getRepository(EstructuraDocumental::class)->getTiposDocumentales($this->em, $page, $query, $items_per_page, $id);
    }
}
