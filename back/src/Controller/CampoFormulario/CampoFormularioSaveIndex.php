<?php

namespace App\Controller\CampoFormulario;

use Symfony\Component\HttpFoundation\Request;

class CampoFormularioSaveIndex
{

    public function __construct(CampoFormularioCreateService $campoFormularioCreateService)
    {
        $this->campoFormularioCreateService = $campoFormularioCreateService;
    }

    public function __invoke(Request $request)
    {

        $data = json_decode($request->getContent());
        return $this->campoFormularioCreateService->saveIndex($data);
    }
}
