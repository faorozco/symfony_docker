<?php

namespace App\Controller;

use App\Entity\Registro;
use App\Entity\Usuario;
use App\Entity\TipoCorrespondencia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RegistryHeaderService
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
        $cabecera = array();
        //Consultar el usuario
        //Verificar que grupos tiene asignado
        $usuario = $this->tokenStorage->getToken()->getUser();
        $sede = $usuario->getSede();

        $registro = $this->em->getRepository(Registro::class)->findOneById($request->attributes->get("id"));
        $usuarioRegistro = $this->em->getRepository(Usuario::class)->findOneById($registro->getUsuarioId());
        $tipoCorrespondencia = $this->em->getRepository(TipoCorrespondencia::class)->findOneById($registro->getTipoCorrespondencia());
        $formulario = $registro->getFormularioVersion();

        $cabecera["Formulario"] = $formulario->getNombre();
        $cabecera["Nomenclatura"] = $formulario->getNomenclaturaFormulario();
        $cabecera["Versión"] = $formulario->getVersion();
        $cabecera["Fecha versión"] = $formulario->getFechaVersion()->format('Y-m-d');
        $cabecera["Radicado"] = $registro->getRadicado();
        $cabecera["Fecha radicación"] = $registro->getFechaHora()->format('Y-m-d h:i A');
        $cabecera["Sede"] = $sede->getNombre();
        $cabecera["Usuario"] = $usuarioRegistro->getLogin();
        $cabecera["Correspondencia"] = $tipoCorrespondencia->getNombre();
        if ($tipoCorrespondencia->getNombre() != "N / A") {
            $cabecera["Consecutivo"] = $registro->getConsecutivo();
        }
        

        if (count($cabecera) > 0) {
            return array("response" => array("response" => $cabecera));
        } else if (count($cabecera) == 0) {
            return array("response" => array("message" => "No hay datos de cabecera"));
        }
    }
}
