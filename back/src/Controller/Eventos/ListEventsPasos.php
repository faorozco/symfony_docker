<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Eventos;

use Symfony\Component\HttpFoundation\Request;

class ListEventsPasos
{

    public function __construct(ListEventsPasosService $ListEventsPasosService)
    {
        $this->ListEventsPasosService = $ListEventsPasosService;
    }

    public function __invoke(Request $request)
    {
        return $this->ListEventsPasosService->Response($request);
    }
}
