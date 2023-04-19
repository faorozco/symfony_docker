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
class ActivateWorkFlowService
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
    public function activate(Request $request)
    {
        $workFlowId = $request->attributes->get("id");
        $activate = false;

        $workFlow = $this->em->getRepository(FlujoTrabajo::class)
            ->findOneById($workFlowId);
        $workFlow->setEstadoId(1);
        $this->em->persist($workFlow);
        $this->em->flush();
        $activate = true;

        return (array("result" => array("response" => $activate)));
    }
}
