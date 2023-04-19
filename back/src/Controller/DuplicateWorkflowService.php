<?php

namespace App\Controller;

use App\Entity\FlujoTrabajo;
use App\Entity\Usuario;
use App\Entity\Paso;
use App\Entity\PasoEvento;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class DuplicateWorkflowService
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
     * Duplicar flujo de trabajo
     *
     * @param string $request
     *
     * @return FlujoTrabajo
     */
    public function duplicateWorkFlow(Request $request)
    {
        //consulto el flujo de trabajo que se quiere duplicar
        $workflow = $this->em->getRepository(FlujoTrabajo::class)->findOneById($request->attributes->get("id"));
        //luego de eso invoco el metodo mágico clone de PHP el cual se sobreescribió en la entidad flujotrabajo
        $workflowClone = new $workflow();
        //Se le cambia el nombre al nuevo flujo de trabajo para identificarlo facilmente.
        $workflowClone->setNombre($workflow->getNombre() . " (Duplicado)");
        $workflowClone->setVersion(0);
        $workflowClone->setDescripcion($workflow->getDescripcion());
        $workflowClone->setEstadoId(0);
        $this->em->persist($workflowClone);
        $this->em->flush();

        foreach($workflow->getPasos() as $paso) {
            $this->duplicatePaso($paso, $workflowClone);
        }

        if (isset($workflowClone)) {
            return $workflowClone;
        } else {
            return array("response" => "FlujoTrabajo no se pudo clonar");
        }
    }

    public function duplicatePaso(Paso $paso, FlujoTrabajo $flujoTrabajo) {
        $pasoClone = new $paso();
        $pasoClone->setFlujoTrabajo($flujoTrabajo);
        $pasoClone->setPrioridad($paso->getPrioridad());
        $pasoClone->setDescripcion($paso->getDescripcion());
        $pasoClone->setEstadoId($paso->getEstadoId());
        $pasoClone->setPlazo($paso->getPlazo());
        $pasoClone->setTime($paso->getTime());
        $pasoClone->setNumero($paso->getNumero());
        $this->em->persist($pasoClone);
        $this->em->flush();

        foreach($paso->getPasoEventos() as $pasoEvento) {
            $pasoClone->addPasoEvento($this->duplicatePasoEvento($pasoEvento, $pasoClone));
        }

        $this->em->persist($pasoClone);
        $this->em->flush();
    }

    public function duplicatePasoEvento(PasoEvento $pasoEvento, Paso $paso) {
        $pasoEventoClone = new $pasoEvento();
        $pasoEventoClone->setPasoId($paso->getId());
        $pasoEventoClone->setPaso($paso);
        $pasoEventoClone->setEvento($pasoEvento->getEvento());
        $pasoEventoClone->setEventoId($pasoEvento->getEventoId());
        $pasoEventoClone->setConfig($pasoEvento->getConfig());
        $pasoEventoClone->setFatherId($pasoEvento->getFatherId());
        $this->em->persist($pasoEventoClone);
        $this->em->flush();
        
        return $pasoEventoClone;
    }
}
