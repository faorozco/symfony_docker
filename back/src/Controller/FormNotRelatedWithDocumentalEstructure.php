<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormNotRelatedWithDocumentalEstructure
{
    public function __construct(FormNotRelatedWithDocumentalEstructureService $FormNotRelatedWithDocumentalEstructureService)
    {
        $this->FormNotRelatedWithDocumentalEstructureService = $FormNotRelatedWithDocumentalEstructureService;
    }

    public function __invoke(Request $request)
    {
        return $this->FormNotRelatedWithDocumentalEstructureService->get($request);
    }
}
