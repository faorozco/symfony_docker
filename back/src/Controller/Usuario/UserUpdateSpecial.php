<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserUpdateSpecial
{
    private $_userUpdateSpecialService;

    public function __construct(UserUpdateSpecialService $userUpdateSpecialService)
    {
        $this->userUpdateSpecialService = $userUpdateSpecialService;
    }

    public function __invoke(Request $request)
    {

        $usuario=$this->userUpdateSpecialService->Actualizar($request);    
        return $usuario;
    }
}