<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DocumentosEstructuraDocumental
{
    public function __construct(DocumentosEstructuraDocumentalService $documentosEstructuraDocumentalService)
    {
        $this->documentosEstructuraDocumentalService = $documentosEstructuraDocumentalService;
    }

    public function __invoke(Request $request)
    {
        return $this->documentosEstructuraDocumentalService->get($request);
    }
}
