<?php

namespace App\Controller\FlujoTrabajoVersion;

use App\Entity\FlujoTrabajoVersion;
use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class FlujoTrabajoVersionAssociateService
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

    public function post($request)
    {
        $formularioVersionId = $request->attributes->get("id");        
        $query = $request->query->get("query");
        $order = $request->query->get("order");


        
        if (isset($formularioVersionId)) {
            $flujosTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->getFlujosAsociados($this->em, $formularioVersionId, $query, $order);
            
            /*isset($descripcion) ||
            isset($estado) ||
            isset($version) ||
            isset($formulario)) {
            $flujoTrabajo= new FlujoTrabajo();
            $flujoTrabajo->setNombre($nombre);
            $flujoTrabajo->setDescripcion($descripcion);
            $flujoTrabajo->setEstadoId($estado);
            $flujoTrabajo->setVersion($version);
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($formulario);
            $flujoTrabajo->setFormulario($formulario);
            $this->em->persist($flujoTrabajo);
            $this->em->flush();*/
            

            return ($flujosTrabajoVersion);
                
        }else{
            return array("response" => "No hay formularios asociados");
        }    
    }
}
