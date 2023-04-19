<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Ciudad;

use Symfony\Component\HttpFoundation\Request;

class CiudadListar
{

    public function __construct(CiudadService $ciudadService)
    {
        $this->ciudadService = $ciudadService;
    }

    public function __invoke(Request $request)
    {
        $filter = $request->get("query");
        $order  = $request->get("orden");
        return $this->ciudadService->list($filter, $order, "false");
    }
}
