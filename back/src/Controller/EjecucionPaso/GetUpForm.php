<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class GetUpForm
{

    public function __construct(GetUpFormService $GetUpFormService)
    {
        $this->GetUpFormService = $GetUpFormService;
    }

    public function __invoke(Request $request)
    {
        return $this->GetUpFormService->get($request);
    }
}
