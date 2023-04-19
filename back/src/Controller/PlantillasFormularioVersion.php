<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class PlantillasFormularioVersion
{
    public function __construct(PlantillasFormularioVersionService $plantillasFormularioVersionService)
    {
        $this->plantillasFormularioVersionService = $plantillasFormularioVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->plantillasFormularioVersionService->get($request);
    }
}
