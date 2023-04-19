<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class AplazarFecha
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionPasoId = $request->attributes->get("id");
        $fecha = $request->query->get("fecha");
        return $this->ejecucionPasoService->aplazarFecha($ejecucionPasoId, $fecha);
    }
}
