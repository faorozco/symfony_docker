<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Registro;

use Symfony\Component\HttpFoundation\Request;

class RegistroRadicado
{
    private $_QRBarCodeViewerService;

    public function __construct(RegistroService $registroService)
    {
        $this->registroService = $registroService;
    }

    public function __invoke(Request $request)
    {
        $radicado = $request->attributes->get("radicado");
        return $this->registroService->registroRadicado($radicado);
    }
}
