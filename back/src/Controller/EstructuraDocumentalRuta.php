<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalRuta
{
    public function __construct(EstructuraDocumentalRutaService $estructuraDocumentalRutaService)
    {
        $this->estructuraDocumentalRutaService = $estructuraDocumentalRutaService;
    }

    public function __invoke(Request $request)
    {
        return $this->estructuraDocumentalRutaService->get($request);
    }
}
