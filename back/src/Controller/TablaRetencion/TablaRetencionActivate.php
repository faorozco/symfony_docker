<?php
// api/src/Controller/NewUser.php

namespace App\Controller\TablaRetencion;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionActivate
{
    private $_tablaRetencionCreateService;

    public function __construct(TablaRetencionActivateService $tablaRetencionActivateService)
    {
        $this->tablaRetencionActivateService = $tablaRetencionActivateService;
    }

    public function __invoke(Request $request)
    {

        $tablaRetencion = $this->tablaRetencionActivateService->save($request);
        return $tablaRetencion;
    }
}
