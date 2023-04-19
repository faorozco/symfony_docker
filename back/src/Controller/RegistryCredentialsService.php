<?php

namespace App\Controller;

use App\Entity\Registro;
use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RegistryCredentialsService
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
    public function Get(Request $request)
    {
        //Consultar el usuario
        $usuario = $this->tokenStorage->getToken()->getUser();
        //Verificar que grupos tiene asignado
        $gruposUsuario = $usuario->getGrupos();
        //Verificar que grupos tiene asignado el formulario que creó el registro
        $registro = $this->em->getRepository(Registro::class)->findOneById($request->attributes->get("id"));
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($registro->getFormularioVersion()->getFormularioId());
        $gruposFormulario = $formulario->getGrupos();

        if (isset($gruposUsuario) && isset($gruposFormulario)) {
            $credentials = array();
            $gruposEnComun = array();
            foreach ($gruposUsuario as $grupoUsuario) {
                foreach ($gruposFormulario as $grupoFormulario) {
                    if ($grupoUsuario === $grupoFormulario) {
                        $gruposEnComun[] = $grupoUsuario;
                    }

                }
            }
            if (isset($gruposEnComun)) {
                $i = 0;
                foreach ($gruposEnComun as $grupoEnComun) {
                    if ($grupoEnComun->getModo() == "U") {
                        $i = 1;
                        return array("response" => array("actualizar" => true));
                    }
                }
                if ($i == 0) {
                    return array("response" => array("actualizar" => false));
                }

                return $credentials;
            } else if (!isset($gruposEnComun)) {
                return array("response" => array("message" => "El usuario no tiene grupos en comun con el formulario que creó este registro"));
            }

            //verificar si los grupos que tiene el usuario si estan en los grupos del formulario
            //Los grupos que queden se verifica si el grupo que tiene asignado el usuario tiene modo A
            //Si tiene el modo A retorna array con "actualizar": [true/false];
        } else if (!isset($gruposUsuario)) {
            return array("response" => array("message" => "El usuario no tiene grupos asignados"));
        } else if (!isset($gruposFormulario)) {
            return array("response" => array("message" => "El formulario no tiene grupos asignados"));
        }
    }
}
