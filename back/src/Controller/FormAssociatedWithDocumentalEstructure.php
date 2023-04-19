<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormAssociatedWithDocumentalEstructure
{
    public function __construct(FormAssociatedWithDocumentalEstructureService $formAssociatedRelatedWithDocumentalEstructureService)
    {
        $this->formAssociatedRelatedWithDocumentalEstructureService = $formAssociatedRelatedWithDocumentalEstructureService;
    }

    public function __invoke(Request $request)
    {
        return $this->formAssociatedRelatedWithDocumentalEstructureService->get($request);
    }
}
