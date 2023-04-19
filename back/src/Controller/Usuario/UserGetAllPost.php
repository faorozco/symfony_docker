<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use Symfony\Component\HttpFoundation\Request;

class UserGetAllPost
{

    public function __construct(UserGetAllPostService $userGetAllPostService)
    {
        $this->userGetAllPostService = $userGetAllPostService;
    }

    public function __invoke(Request $request)
    {
        return $this->userGetAllPostService->post($request);
    }
}
