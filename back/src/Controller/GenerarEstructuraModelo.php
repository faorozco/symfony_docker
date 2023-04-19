<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class GenerarEstructuraModelo
{
    public function __construct(GenerarEstructuraModeloService $generarEstructuraModeloService)
    {
        $this->generarEstructuraModeloService = $generarEstructuraModeloService;
    }

    public function __invoke(Request $request)
    {
        return $this->generarEstructuraModeloService->generate($request);
    }
}
