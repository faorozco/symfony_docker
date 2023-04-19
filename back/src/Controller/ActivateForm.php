<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ActivateForm
{
    public function __construct(ActivateFormService $activateFormService)
    {
        $this->activateFormService = $activateFormService;
    }

    public function __invoke(Request $request)
    {
        return $this->activateFormService->activate($request);
    }
}
