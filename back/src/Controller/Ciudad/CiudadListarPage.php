<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Ciudad;

use Symfony\Component\HttpFoundation\Request;

class CiudadListarPage
{

    public function __construct(CiudadService $ciudadService)
    {
        $this->ciudadService = $ciudadService;
    }

    public function __invoke(Request $request)
    {
        $filter = $request->get("query");
        $order  = $request->get("orden");
        $mostrarInactivos  = $request->get("mostrarInactivos");
        $page  = $request->get("page");
        $size  = $request->get("size");
        return $this->ciudadService->listPage($filter, $order, $mostrarInactivos, $page, $size);
    }
}
