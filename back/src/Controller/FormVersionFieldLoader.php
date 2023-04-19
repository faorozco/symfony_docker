<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormVersionFieldLoader
{

    public function __construct(FormVersionFieldLoaderService $formVersionFieldLoaderService)
    {
        $this->formVersionFieldLoaderService = $formVersionFieldLoaderService;
    }

    public function __invoke(Request $request)
    {
        return $this->formVersionFieldLoaderService->get($request);
    }
}
