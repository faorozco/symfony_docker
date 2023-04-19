<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class PlantillasPasoVersion
{
    public function __construct(PlantillasPasoVersionService $plantillasPasoVersionService)
    {
        $this->plantillasPasoVersionService = $plantillasPasoVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->plantillasPasoVersionService->get($request);
    }
}
