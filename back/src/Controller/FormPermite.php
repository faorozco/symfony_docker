<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormPermite
{

    public function __construct(FormPermiteService $formPermiteService)
    {
        $this->formPermiteService = $formPermiteService;
    }

    public function __invoke(Request $request)
    {
        return $this->formPermiteService->get($request);
    }
}
