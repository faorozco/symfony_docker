<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class GenerarEstructuraModeloVersion
{
    public function __construct(GenerarEstructuraModeloVersionService $generarEstructuraModeloVersionService)
    {
        $this->generarEstructuraModeloVersionService = $generarEstructuraModeloVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->generarEstructuraModeloVersionService->generate($request);
    }
}
