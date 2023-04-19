<?php

namespace App\Controller;

use App\Entity\Formulario;
use App\Entity\Registro;
use App\Entity\TablaRetencion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class DocumentosEstructuraDocumentalService
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
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $orderBy = $request->query->get('order');
        $itemsPerPage = $request->attributes->get('_items_per_page');
        //Primero capturo el id de la estructura a consultar
        $estructuraDocumentalId = $request->attributes->get("id");
        $trd = $this->em->getRepository(TablaRetencion::class)->findOneBy(array("estructura_documental_id" => $estructuraDocumentalId));
        if (isset($trd)) {
            //Consultar el formulario relacionado a la TRD
            $formulario = $this->em->getRepository(Formulario::class)->findOneBy(array("tabla_retencion_id" => $trd->getId()));
            if (isset($formulario)) {
                // Consultar el usuario para validar que grupos tiene asignado
                // y corroborar que puede tener acceso a los documentos creados con el formulario de
                // la estructura documental escogida
                $usuario = $this->tokenStorage->getToken()->getUser();
                //Verificar que grupos tiene asignado
                $gruposUsuario = $usuario->getGrupos();
                //Verificar que grupos tiene asignado el formulario
                $gruposFormulario = $formulario->getGrupos();
                //Veriricar si hay grupos relacionados entre formulario y usuario
                if (isset($gruposUsuario) && isset($gruposFormulario)) {
                    $gruposEnComun = array();
                    foreach ($gruposUsuario as $grupoUsuario) {
                        foreach ($gruposFormulario as $grupoFormulario) {
                            if ($grupoUsuario === $grupoFormulario) {
                                $gruposEnComun[] = $grupoUsuario;
                            }
                        }
                    }
                    if (count($gruposEnComun) > 0) {
                        //Consultar los registros relacionados al formulario de la TRD de la estructura documental
                        $registros = $this->em->getRepository(Registro::class)->findRegistrosByEstructuraDocumental($this->em, $formulario->getId(), $page, $query, $orderBy["id"], $itemsPerPage);
                        return ($registros);
                    }
                } else if (!isset($gruposUsuario)) {
                    return array("response" => array("message" => "El usuario no tiene grupos asignados"));
                } else if (!isset($gruposFormulario)) {
                    return array("response" => array("message" => "El formulario no tiene grupos asignados"));
                }
                return array("response" => array("documents" => $documents));
            } else {
                return array("response" => array("message" => "El formulario no tiene TRD relacionada"));
            }
        } else if (!isset($trd)) {
            return array("response" => array("message" => "Esta estructura documental no tiene TRD relacionada"));
        }
    }
}
