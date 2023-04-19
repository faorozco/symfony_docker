<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalImport
{

    public function __construct(EstructuraDocumentalImportService $estructuraDocumentalImportService)
    {
        $this->estructuraDocumentalImportService = $estructuraDocumentalImportService;
    }

    public function __invoke(Request $request)
    {

        return $this->estructuraDocumentalImportService->Import($request);
    }
}
