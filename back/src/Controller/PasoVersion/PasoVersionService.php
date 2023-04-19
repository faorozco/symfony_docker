<?php

namespace App\Controller\PasoVersion;

use App\Entity\EjecucionFlujo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Registro;
use App\Entity\EjecucionPaso;
use App\Entity\FlujoTrabajoVersion;
use App\Entity\PasoVersion;
use App\Entity\PasoEventoVersion;
use App\Entity\Usuario;
use App\Exceptions\ExecutionException;
use App\Utils\Constant\ResponseCode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Undocumented class
 */
class PasoVersionService
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

    public function cargarAcciones($pasoVersionId)
    {  
        $eventosVersion = $this->em->getRepository(PasoEventoVersion::class)->findBy(array("paso_version_id" => $pasoVersionId));
        
        if (isset($eventosVersion) && count($eventosVersion) > 0) {
            $eventos = array();
            foreach($eventosVersion as $evento) {
                $eventos[$evento->getFatherId()] = array(
                    "id" => $evento->getId(),
                    "eventoId" => $evento->getEventoId(),
                    "fatherId" => $evento->getFatherId(),
                    "config" => $evento->getConfig()
                );
            }

            return array("acciones" => $eventos);
        } else {
            return array("response" => "No tiene eventos asociados");
        }
    }

    public function cargarAccionesFlujoTrabajo($request)
    {     
        $flujoTrabajoVersionId = $request->attributes->get("id");
        $pasoNumero = $request->query->get("pasoNumero");
        $pasoVersion = $this->em->getRepository(PasoVersion::class)->findBy(array("flujo_trabajo_version_id" => $flujoTrabajoVersionId, "numero" => $pasoNumero))[0];
        
        return $this->cargarAcciones($pasoVersion->getId());
    }
}
