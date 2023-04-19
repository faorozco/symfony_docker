<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ProcesarEstructuraModeloVersion
{
    public function __construct(ProcesarEstructuraModeloVersionService $procesarEstructuraModeloVersionService)
    {
        $this->procesarEstructuraModeloVersionService = $procesarEstructuraModeloVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->procesarEstructuraModeloVersionService->process($request);
    }
}
