<?php
// api/src/Controller/NewUser.php

namespace App\Controller\TablaRetencion;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionInactivate
{

    public function __construct(TablaRetencionInactivateService $tablaRetencionInactivateService)
    {
        $this->tablaRetencionInactivateService = $tablaRetencionInactivateService;
    }

    public function __invoke(Request $request)
    {

        $tablaRetencion = $this->tablaRetencionInactivateService->save($request);
        return $tablaRetencion;
    }
}
