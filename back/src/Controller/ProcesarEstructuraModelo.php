<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ProcesarEstructuraModelo
{
    public function __construct(ProcesarEstructuraModeloService $procesarEstructuraModeloService)
    {
        $this->procesarEstructuraModeloService = $procesarEstructuraModeloService;
    }

    public function __invoke(Request $request)
    {
        return $this->procesarEstructuraModeloService->process($request);
    }
}
