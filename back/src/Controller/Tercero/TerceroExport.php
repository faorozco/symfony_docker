<?php

namespace App\Controller\Tercero;

use Symfony\Component\HttpFoundation\Request;

class TerceroExport
{
    private $_QRBarCodeViewerService;

    public function __construct(TerceroExportService $terceroExportService)
    {
        $this->terceroExportService = $terceroExportService;
    }

    public function __invoke(Request $request)
    {
        return $this->terceroExportService->Export($request);
    }
}
