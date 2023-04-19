<?php

namespace App\Controller;

use App\Entity\EstructuraDocumentalVersion;
use App\Entity\FormularioVersion;
use App\Entity\Formulario;
use App\Entity\Registro;
use App\Entity\TablaRetencionVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class DocumentosEstructuraDocumentalVersionService
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
        $estructuraDocumentalVersionId = $request->attributes->get("id");
        
        $formularios = $this->em->getRepository(FormularioVersion::class)->getFormulariosPorEstructuraDocumental($this->em, $estructuraDocumentalVersionId);

        $registros = new ArrayCollection();
        // Consultar el usuario para validar que grupos tiene asignado
        // y corroborar que puede tener acceso a los documentos creados con el formularioVersion de
        // la estructura documental escogida
        $usuario = $this->tokenStorage->getToken()->getUser();
        //Verificar que grupos tiene asignado
        $gruposUsuario = $usuario->getGrupos();

        $formulariosId = new ArrayCollection();
        foreach($formularios as $formularioVersion) {
            
            //Verificar que grupos tiene asignado el formularioVersion
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($formularioVersion->getFormularioId());
            $gruposFormulario = $formulario->getGrupos();
            //Veriricar si hay grupos relacionados entre formularioVersion y usuario
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
                    $formulariosId[] = $formularioVersion->getId();
                }
            }
        }

        if(count($formulariosId)) {
            $registros = $this->em->getRepository(Registro::class)->findRegistrosByFormularioVersionId($formulariosId, $page, $query, $orderBy["id"], $itemsPerPage);
        }

        return ($registros);
    }

    public function get2(Request $request)
    {
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $orderBy = $request->query->get('order');
        $itemsPerPage = $request->attributes->get('_items_per_page');
        //Primero capturo el id de la estructura a consultar
        $estructuraDocumentalVersionId = $request->attributes->get("id");
        $trd = $this->em->getRepository(TablaRetencionVersion::class)->findOneBy(array("estructura_documental_version_id" => $estructuraDocumentalVersionId));
        if (isset($trd)) {
            //Consultar el formularioVersion relacionado a la TRD
            $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneBy(array("estructura_documental_version_id" => $estructuraDocumentalVersionId));
            if (isset($formularioVersion)) {
                // Consultar el usuario para validar que grupos tiene asignado
                // y corroborar que puede tener acceso a los documentos creados con el formularioVersion de
                // la estructura documental escogida
                $usuario = $this->tokenStorage->getToken()->getUser();
                //Verificar que grupos tiene asignado
                $gruposUsuario = $usuario->getGrupos();
                //Verificar que grupos tiene asignado el formularioVersion
                $formulario = $this->em->getRepository(Formulario::class)->findOneById($formularioVersion->getFormularioId());
                $gruposFormulario = $formulario->getGrupos();
                //Veriricar si hay grupos relacionados entre formularioVersion y usuario
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
                        //Consultar los registros relacionados al formularioVersion de la TRD de la estructura documental
                        $registros = $this->em->getRepository(Registro::class)->findRegistrosByEstructuraDocumentalVersion($this->em, $formularioVersion->getId(), $page, $query, $orderBy["id"], $itemsPerPage);
                        return ($registros);
                    }
                } else if (!isset($gruposUsuario)) {
                    return array("response" => array("message" => "El usuario no tiene grupos asignados"));
                } else if (!isset($gruposFormulario)) {
                    return array("response" => array("message" => "El formularioVersion no tiene grupos asignados"));
                }
                return array("response" => array("documents" => $documents));
            } else {
                return array("response" => array("message" => "El formularioVersion no tiene TRD relacionada"));
            }
        } else if (!isset($trd)) {
            return array("response" => array("message" => "Esta estructura documental no tiene TRD relacionada"));
        }
    }
}
