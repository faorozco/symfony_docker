<?php

namespace App\Controller;

use App\Entity\Notificado;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class NotifiedNotificacionsService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {

        $this->tokenStorage = $tokenStorage;
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
        // $order = $request->query->get('order');
        $items_per_page = $request->attributes->get('_items_per_page');
        $estado_id = $request->attributes->get('_estado_id');
        $order_by = $request->query->get('order');
        $query = $request->query->get('query');
        $estadoTarea = $request->query->get('estadoTarea');
        $tipoFormulario = $request->query->get('tipoFormulario');
        $visto = $request->query->get('visto');
        $result = $this->em->getRepository(Notificado::class)->getNotifiedNotificacions($items_per_page, $page, $estado_id, $order_by, $estadoTarea, $tipoFormulario, $visto, $query);

        $notificados = array();

        foreach ($result as $nota) {

            $permites = array();

            if (isset($nota['formulario_id']) && $nota['formulario_id'] > 0) {
                $permites = $this->permitesFormularioByGrupo($nota['formulario_id']);
            }

            $nota['permites'] = $permites;

            $notificados[] = $nota;
        }

        if (isset($notificados)) {
            return $notificados;

        } else {
            return array("response" => "En horabuena! No tienes notificaciones pendientes");
        }
    }

    public function permitesFormularioByGrupo($idForm)
    {

        $usuario = $this->tokenStorage->getToken()->getUser();

        $gruposUsuario = $usuario->getGrupos();

        $gruposSeguridad = array();

        foreach ($gruposUsuario as $grupo) {
            $gruposSeguridad[] = $grupo->getId();
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $entityResults = $queryBuilder
            ->select("permite.id, permite.nombre, opciones.grupos")
            ->from('App\\Entity\\Permite', 'permite')
            ->join('permite.opcionFormularios', 'opciones', Expr\Join::WITH, 'opciones.formulario_id = :form_id')
            ->setParameter('form_id', $idForm)
            ->orderBy('permite.nombre', 'ASC')
            ->getQuery()
            ->execute();

        $permites = array();

        foreach ($entityResults as $permite) {

            $gruposAccion = $permite['grupos'];
            if (isset($gruposAccion['grupos'])) {
                foreach ($gruposAccion['grupos'] as $value) {
                    if (in_array($value['id'], $gruposSeguridad)) {
                        $permites[] = array('id' => $permite['id'], 'nombre' => $permite['nombre']);
                    }
                }
            }
        }

        return $permites;

    }
}
