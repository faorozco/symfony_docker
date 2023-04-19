<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DuplicateForm
{

    public function __construct(DuplicateFormService $duplicateFormService)
    {
        $this->duplicateFormService = $duplicateFormService;
    }

    public function __invoke(Request $request)
    {
        return $this->duplicateFormService->Duplicate($request);
    }
}
