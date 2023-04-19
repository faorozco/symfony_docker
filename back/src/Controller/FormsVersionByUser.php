<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormsVersionByUser
{

    public function __construct(FormsVersionByUserService $formsVersionByUserService)
    {
        $this->formsVersionByUserService = $formsVersionByUserService;
    }

    public function __invoke(Request $request)
    {
        $query = $request->query->get('query');
        return $this->formsVersionByUserService->get($query);
    }
}
