<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CargoImport
{
    private $_QRBarCodeViewerService;

    public function __construct(CargoImportService $cargoImportService)
    {
        $this->cargoImportService = $cargoImportService;
    }

    public function __invoke(Request $request)
    {
        
        return $this->cargoImportService->Import($request);
    }
}
