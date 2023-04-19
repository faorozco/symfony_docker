<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use Symfony\Component\HttpFoundation\Request;

class UserGetId
{

    public function __construct(UserGetIdService $UserGetIdService)
    {
        $this->UserGetIdService = $UserGetIdService;
    }

    public function __invoke(Request $request)
    {
        return $this->UserGetIdService->get($request);
    }
}
