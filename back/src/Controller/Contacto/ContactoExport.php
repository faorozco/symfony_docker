<?php

namespace App\Controller\Contacto;

use Symfony\Component\HttpFoundation\Request;

class ContactoExport
{
    private $_QRBarCodeViewerService;

    public function __construct(ContactoExportService $contactoExportService)
    {
        $this->contactoExportService = $contactoExportService;
    }

    public function __invoke(Request $request)
    {
        return $this->contactoExportService->Export($request);
    }
}
