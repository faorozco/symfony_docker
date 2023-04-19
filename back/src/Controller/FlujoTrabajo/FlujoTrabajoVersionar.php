<?php
// api/src/Controller/NewUser.php

namespace App\Controller\FlujoTrabajo;

use Symfony\Component\HttpFoundation\Request;

class FlujoTrabajoVersionar
{
    public function __construct(FlujoTrabajoService $FlujoTrabajoService)
    {
        $this->FlujoTrabajoService = $FlujoTrabajoService;
    }

    public function __invoke(Request $request)
    {
        return $this->FlujoTrabajoService->version($request);
    }
}
