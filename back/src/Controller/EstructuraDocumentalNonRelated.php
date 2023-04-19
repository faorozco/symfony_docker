<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalNonRelated
{
    private $_estructuraDocumentalNonRelatedService;

    public function __construct(EstructuraDocumentalNonRelatedService $estructuraDocumentalNonRelatedService)
    {
        $this->estructuraDocumentalNonRelatedService = $estructuraDocumentalNonRelatedService;
    }

    public function __invoke(Request $request)
    {
        $estructuraDocumental = $this->estructuraDocumentalNonRelatedService->getNonRelated($request);
        return $estructuraDocumental;
    }
}
