<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CampoFormularioUpdate
{

    public function __construct(CampoFormularioUpdateService $campoFormularioUpdateService)
    {
        $this->campoFormularioUpdateService = $campoFormularioUpdateService;
    }

    public function __invoke(Request $request)
    {
        return $this->campoFormularioUpdateService->put($request);
    }
}
