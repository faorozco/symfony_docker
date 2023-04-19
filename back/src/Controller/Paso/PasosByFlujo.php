<?php

namespace App\Controller\Paso;

use Symfony\Component\HttpFoundation\Request;

class PasosByFlujo
{
    public function __construct(PasosByFlujoService $pasosByFlujoService)
    {
        $this->pasosByFlujoService = $pasosByFlujoService;
    }

    public function __invoke(Request $request)
    {
        return $this->pasosByFlujoService->Get($request);
    }
}
