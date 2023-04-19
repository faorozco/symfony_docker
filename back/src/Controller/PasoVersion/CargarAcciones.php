<?php

namespace App\Controller\PasoVersion;

use Symfony\Component\HttpFoundation\Request;

class CargarAcciones
{

    public function __construct(PasoVersionService $pasoVersionService)
    {
        $this->pasoVersionService = $pasoVersionService;
    }

    public function __invoke(Request $request)
    {
        $pasoVersionId = $request->attributes->get("id");
        return $this->pasoVersionService->cargarAcciones($pasoVersionId);
    }
}
