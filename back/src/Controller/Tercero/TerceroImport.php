<?php

namespace App\Controller\Tercero;

use Symfony\Component\HttpFoundation\Request;

class TerceroImport
{
    private $_QRBarCodeViewerService;

    public function __construct(TerceroImportService $terceroImportService)
    {
        $this->terceroImportService = $terceroImportService;
    }

    public function __invoke(Request $request)
    {
        
        return $this->terceroImportService->Import($request);
    }
}
