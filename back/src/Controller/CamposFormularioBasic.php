<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CamposFormularioBasic
{
    private $_QRBarCodeViewerService;

    public function __construct(CamposFormularioBasicService $camposFormularioBasicService)
    {
        $this->camposFormularioBasicService = $camposFormularioBasicService;
    }

    public function __invoke(Request $request)
    {
        return $this->camposFormularioBasicService->Get($request);
    }
}
