<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CargoExport
{
    private $_QRBarCodeViewerService;

    public function __construct(CargoExportService $cargoExportService)
    {
        $this->cargoExportService = $cargoExportService;
    }

    public function __invoke(Request $request)
    {
        return $this->cargoExportService->Export($request);
    }
}
