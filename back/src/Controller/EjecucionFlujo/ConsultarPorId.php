<?php

namespace App\Controller\EjecucionFlujo;

use Symfony\Component\HttpFoundation\Request;

class ConsultarPorId
{

    public function __construct(EjecucionFlujoService $ejecucionFlujoService)
    {
        $this->ejecucionFlujoService = $ejecucionFlujoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionFlujoId = $request->attributes->get("id");

        return $this->ejecucionFlujoService->consultarPorId($ejecucionFlujoId);
    }
}
