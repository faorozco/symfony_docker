<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserGetAll
{

    public function __construct(UserGetAllService $userGetAllService)
    {
        $this->userGetAllService = $userGetAllService;
    }

    public function __invoke(Request $request)
    {
        return $this->userGetAllService->get($request);
    }
}
