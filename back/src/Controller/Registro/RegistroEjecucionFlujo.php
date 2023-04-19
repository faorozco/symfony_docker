<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Registro;

use Symfony\Component\HttpFoundation\Request;

class RegistroEjecucionFlujo
{
    private $_QRBarCodeViewerService;

    public function __construct(RegistroService $registroService)
    {
        $this->registroService = $registroService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionFlujoId = $request->attributes->get("id");
        return $this->registroService->registroEjecucionFlujo($ejecucionFlujoId);
    }
}
