<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ContactoImport
{
    private $_QRBarCodeViewerService;

    public function __construct(ContactoImportService $contactoImportService)
    {
        $this->contactoImportService = $contactoImportService;
    }

    public function __invoke(Request $request)
    {
        
        return $this->contactoImportService->Import($request);
    }
}
