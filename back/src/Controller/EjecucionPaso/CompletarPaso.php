<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class CompletarPaso
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        return $this->ejecucionPasoService->completar($request);
    }
}
