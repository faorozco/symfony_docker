<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class GetFormFlujo
{

    public function __construct(GetFormFlujoService $getFormFlujoService)
    {
        $this->getFormFlujoService = $getFormFlujoService;
    }

    public function __invoke(Request $request)
    {
        return $this->getFormFlujoService->get($request);
    }
}
