<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class GroupCreate
{
    private $_groupCreateService;

    public function __construct(GroupCreateService $groupCreateService)
    {
        $this->groupCreateService = $groupCreateService;
    }

    public function __invoke(Request $request)
    {

        return $this->groupCreateService->create($request);        
    }
}