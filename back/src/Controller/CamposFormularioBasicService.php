<?php

namespace App\Controller;

use App\Entity\CampoFormulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class CamposFormularioBasicService
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
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $formulario_id = $request->attributes->get('id');
        $order_by = $request->query->get('order');
        $fields = $this->em->getRepository(CampoFormulario::class)->findBasicFields($formulario_id, $page, $query, $order_by);
        if (isset($fields)) {
            return $fields;
        } else {
            return array("response" => "Formulario no tiene campos básicos asignados");
        }
    }
}
