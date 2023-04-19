<?php

namespace App\Controller\Contacto;

use Symfony\Component\HttpFoundation\Request;

class ContactoDownloadModel
{
    private $_QRBarCodeViewerService;

    public function __construct(ContactoDownloadModelService $contactoDownloadModelService)
    {
        $this->contactoDownloadModelService = $contactoDownloadModelService;
    }

    public function __invoke(Request $request)
    {
        return $this->contactoDownloadModelService->download($request);
    }
}
