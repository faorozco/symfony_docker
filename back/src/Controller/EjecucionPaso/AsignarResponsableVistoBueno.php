<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class AsignarResponsableVistoBueno
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionPasoId = $request->attributes->get("id");
        $usuarioId = $request->query->get("usuarioId");
        return $this->ejecucionPasoService->asignarResponsableVistoBueno($ejecucionPasoId, $usuarioId);
    }
}
