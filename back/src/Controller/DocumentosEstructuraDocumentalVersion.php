<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DocumentosEstructuraDocumentalVersion
{
    public function __construct(DocumentosEstructuraDocumentalVersionService $documentosEstructuraDocumentalVersionService)
    {
        $this->documentosEstructuraDocumentalVersionService = $documentosEstructuraDocumentalVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->documentosEstructuraDocumentalVersionService->get($request);
    }
}
