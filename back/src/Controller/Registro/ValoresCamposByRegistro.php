<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Registro;

use Symfony\Component\HttpFoundation\Request;

class ValoresCamposByRegistro
{
    private $_QRBarCodeViewerService;

    public function __construct(RegistroService $registroService)
    {
        $this->registroService = $registroService;
    }

    public function __invoke(Request $request)
    {
        return $this->registroService->cargarRegistroPorFormularioId($request);
    }
}
