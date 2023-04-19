<?php

namespace App\Controller\EjecucionFlujo;

use Symfony\Component\HttpFoundation\Request;

class IniciarFlujo
{

    public function __construct(EjecucionFlujoService $ejecucionFlujoService)
    {
        $this->ejecucionFlujoService = $ejecucionFlujoService;
    }

    public function __invoke(Request $request)
    {
        return $this->ejecucionFlujoService->iniciar($request);
    }
}
