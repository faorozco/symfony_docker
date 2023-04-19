<?php
// api/src/Controller/UserByLogin.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserByLogin
{
    private $_userByLoginService;

    public function __construct(UserByLoginService $userByLoginService)
    {
        $this->userByLoginService = $userByLoginService;
    }

    public function __invoke(Request $request)
    {
        $login = $request->query->get('login');
        $user = $this->userByLoginService->get($login);
        return $user;
    }
}
