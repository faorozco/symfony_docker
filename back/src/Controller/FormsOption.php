<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormsOption
{

    public function __construct(FormsOptionService $formsOptionService)
    {
        $this->formsOptionService = $formsOptionService;
    }

    public function __invoke(Request $request)
    {

        $id = $request->attributes->get("id");
        return $this->formsOptionService->get($id);
    }
}
