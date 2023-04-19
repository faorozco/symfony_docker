<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class CambiarArchivo
{
    public function __construct(CambiarArchivoService $cambiarArchivoService)
    {
        $this->cambiarArchivoService = $cambiarArchivoService;
    }

    public function __invoke(Request $request)
    {
        return $this->cambiarArchivoService->save($request);
    }
}