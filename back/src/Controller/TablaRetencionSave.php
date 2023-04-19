<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionSave
{
    private $_tablaRetencionCreateService;

    public function __construct(TablaRetencionSaveService $tablaRetencionSaveService)
    {
        $this->tablaRetencionSaveService = $tablaRetencionSaveService;
    }

    public function __invoke(Request $request)
    {

        $tablaRetencion = $this->tablaRetencionSaveService->save($request);
        return $tablaRetencion;
    }
}
