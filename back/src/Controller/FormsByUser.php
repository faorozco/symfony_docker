<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormsByUser
{

    public function __construct(FormsByUserService $formsByUserService)
    {
        $this->formsByUserService = $formsByUserService;
    }

    public function __invoke(Request $request)
    {
        $query = $request->query->get('query');
        return $this->formsByUserService->get($query);
    }
}
