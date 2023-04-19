<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormByDocumentalEstructureId
{

    public function __construct(FormByDocumentalEstructureIdService $formByDocumentalEstructureIdService)
    {
        $this->formByDocumentalEstructureIdService = $formByDocumentalEstructureIdService;
    }

    public function __invoke(Request $request)
    {
        return $this->formByDocumentalEstructureIdService->get($request);
    }
}
