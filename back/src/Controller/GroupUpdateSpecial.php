<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class GroupUpdateSpecial
{
    private $_groupUpdateSpecialService;

    public function __construct(GroupUpdateSpecialService $groupUpdateSpecialService)
    {
        $this->groupUpdateSpecialService = $groupUpdateSpecialService;
    }

    public function __invoke(Request $request)
    {

        return $this->groupUpdateSpecialService->Update($request);        
    }
}