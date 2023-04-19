<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalExport
{
    private $_QRBarCodeViewerService;

    public function __construct(EstructuraDocumentalExportService $estructuraDocumentalExportService)
    {
        $this->estructuraDocumentalExportService = $estructuraDocumentalExportService;
    }

    public function __invoke(Request $request)
    {
        return $this->estructuraDocumentalExportService->Export($request);
    }
}
