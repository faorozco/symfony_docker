<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ProcesoExport
{
    private $_QRBarCodeViewerService;

    public function __construct(ProcesoExportService $procesoExportService)
    {
        $this->procesoExportService = $procesoExportService;
    }

    public function __invoke(Request $request)
    {
        return $this->procesoExportService->Export($request);
    }
}
