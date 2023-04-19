<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionUpdateSpecial
{
    private $_tablaRetencionUpdateSpecialService;

    public function __construct(TablaRetencionUpdateSpecialService $tablaRetencionUpdateSpecialService)
    {
        $this->tablaRetencionUpdateSpecialService = $tablaRetencionUpdateSpecialService;
    }

    public function __invoke(Request $request)
    {

        $tablaRetencion = $this->tablaRetencionUpdateSpecialService->Actualizar($request);
        return $tablaRetencion;
    }
}
