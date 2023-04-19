<?php

namespace App\Controller;

use App\Entity\FlujoTrabajo;
use App\Entity\TablaRetencion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class InactivateWorkFlowService
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
    public function inactivate(Request $request)
    {
        //Capturar el formulario a Inactivar
        $workFlowId = $request->attributes->get("id");
        $inactivate = false;
        //Carga el objeto Formulario relacionado al id entregado
        $workFlow = $this->em->getRepository(FlujoTrabajo::class)
            ->findOneById($workFlowId);
        $workFlow->setEstadoId(0);
        $this->em->persist($workFlow);
        $this->em->flush();
        $inactivate = true;

        return (array("result" => array("response" => $inactivate)));
    }
}
