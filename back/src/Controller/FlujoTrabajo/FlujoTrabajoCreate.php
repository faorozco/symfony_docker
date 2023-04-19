<?php

namespace App\Controller\FlujoTrabajo;

use Symfony\Component\HttpFoundation\Request;

class FlujoTrabajoCreate
{

    public function __construct(FlujoTrabajoService $flujoTrabajoService)
    {
        $this->flujoTrabajoService = $flujoTrabajoService;
    }

    public function __invoke(Request $request)
    {
        return $this->flujoTrabajoService->create($request);
    }
}
