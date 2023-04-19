<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class SiguientePaso
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionPasoId = $request->attributes->get("id");
        return $this->ejecucionPasoService->siguientePasoVersion($ejecucionPasoId);
    }
}
