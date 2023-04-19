<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormFieldLoader
{

    public function __construct(FormFieldLoaderService $formFieldLoaderService)
    {
        $this->formFieldLoaderService = $formFieldLoaderService;
    }

    public function __invoke(Request $request)
    {
        return $this->formFieldLoaderService->get($request);
    }
}
