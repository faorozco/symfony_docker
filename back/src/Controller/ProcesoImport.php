<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ProcesoImport
{
    private $_QRBarCodeViewerService;

    public function __construct(ProcesoImportService $procesoImportService)
    {
        $this->procesoImportService = $procesoImportService;
    }

    public function __invoke(Request $request)
    {
        
        return $this->procesoImportService->Import($request);
    }
}
