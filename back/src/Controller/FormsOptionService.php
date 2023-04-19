<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FormsOptionService
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

    public function get($id)
    {

        $queryBuilder = $this->em->createQueryBuilder();
        $entityResults = $queryBuilder
            ->select("permite.id, permite.nombre, permite.acciones, opciones.id as opcion_formularios, opciones.configuraciones, opciones.acciones as acciones_configuraciones")
            ->from('App\\Entity\\Permite', 'permite')
            ->leftJoin('permite.opcionFormularios', 'opciones', Expr\Join::WITH, 'opciones.formulario_id = :form_id')
            ->setParameter('form_id', $id)
            ->orderBy('permite.orden', 'ASC')
            ->getQuery()
            ->execute();

        $result = array();

        foreach ($entityResults as $value) {

            $configuraciones = $value['configuraciones'];

            /*if (isset($value['configuraciones']) && !empty($value['configuraciones'])) {
                $configuraciones = array_values($value['configuraciones']);
            }*/

            $permite = array();

            $permite['id'] = $value['id'];
            $permite['nombre'] = $value['nombre'];
            $permite['opcion_formularios'] = $value['opcion_formularios'];

            $arraAcciones = array();

            if ($value['acciones']) {

                foreach ($value['acciones'] as $acc) {

                    if (isset($value['acciones_configuraciones'][$acc])) {
                        $arraAcciones[] = array("nombre" => $acc, "grupos" => $value['acciones_configuraciones'][$acc]);
                    } else {
                        $arraAcciones[] = array("nombre" => $acc, "grupos" => array());
                    }
                }

            }

            $permite['acciones'] = $arraAcciones;

            $result[] = array('permite' => $permite, 'configuraciones' => $configuraciones);
        }

        return $result;

    }
}
