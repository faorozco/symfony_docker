<?php

namespace App\Controller\PasoVersion;

use Symfony\Component\HttpFoundation\Request;

class CargarAccionesFlujoTrabajo
{

    public function __construct(PasoVersionService $pasoVersionService)
    {
        $this->pasoVersionService = $pasoVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->pasoVersionService->cargarAccionesFlujoTrabajo($request);
    }
}
