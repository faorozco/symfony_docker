<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Registro;

use Symfony\Component\HttpFoundation\Request;

class RegistroPorId
{
    private $_QRBarCodeViewerService;

    public function __construct(RegistroService $registroService)
    {
        $this->registroService = $registroService;
    }

    public function __invoke(Request $request)
    {
        $registroId = $request->attributes->get("id");
        return $this->registroService->registroPorId($registroId);
    }
}
