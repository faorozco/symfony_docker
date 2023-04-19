<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class InactivateForm
{
    public function __construct(InactivateFormService $inactivateFormService)
    {
        $this->inactivateFormService = $inactivateFormService;
    }

    public function __invoke(Request $request)
    {
        return $this->inactivateFormService->inactivate($request);
    }
}
