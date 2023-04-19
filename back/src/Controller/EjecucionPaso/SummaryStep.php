<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class SummaryStep
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionFlujoId = $request->attributes->get("ejecucionFlujoId");
        return $this->ejecucionPasoService->summaryStep($ejecucionFlujoId);
    }
}
