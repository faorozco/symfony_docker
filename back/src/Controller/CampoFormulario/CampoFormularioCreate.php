<?php

namespace App\Controller\CampoFormulario;

use Symfony\Component\HttpFoundation\Request;

class CampoFormularioCreate
{

    public function __construct(CampoFormularioCreateService $campoFormularioCreateService)
    {
        $this->campoFormularioCreateService = $campoFormularioCreateService;
    }

    public function __invoke(Request $request)
    {
        return $this->campoFormularioCreateService->put($request);
    }
}
