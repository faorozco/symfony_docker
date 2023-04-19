<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Entidad;

use Symfony\Component\HttpFoundation\Request;

class EntidadColumnas
{

    public function __construct(EntidadService $entidadService)
    {
        $this->entidadService = $entidadService;
    }

    public function __invoke(Request $request)
    {
        $entidadId = $request->attributes->get("id");
        $filter = $request->query->get("filter");
        return $this->entidadService->entidadColumnas($entidadId, $filter);
    }
}