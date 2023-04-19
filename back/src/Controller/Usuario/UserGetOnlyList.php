<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use Symfony\Component\HttpFoundation\Request;

class UserGetOnlyList
{

    public function __construct(UserGetOnlyListService $userGetOnlyListService)
    {
        $this->userGetOnlyListService = $userGetOnlyListService;
    }

    public function __invoke(Request $request)
    {
        return $this->userGetOnlyListService->post($request);
    }
}
