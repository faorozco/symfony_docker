<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionImport
{

    public function __construct(TablaRetencionImportService $tablaRetencionImportService)
    {
        $this->tablaRetencionImportService = $tablaRetencionImportService;
    }

    public function __invoke(Request $request)
    {

        return $this->tablaRetencionImportService->Import($request);
    }
}
