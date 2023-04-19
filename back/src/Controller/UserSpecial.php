<?php
// api/src/Controller/NewUser.php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserSpecial
{
    private $_userSpecialService;

    public function __construct(UserSpecialService $userSpecialService)
    {
        $this->userSpecialService = $userSpecialService;
    }

    public function __invoke(Request $request)
    {

        $usuario=$this->userSpecialService->Crear($request);    
        return $usuario;
    }
}