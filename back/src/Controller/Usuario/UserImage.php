<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use Symfony\Component\HttpFoundation\Request;

class UserImage
{
    private $_userImageService;

    public function __construct(UserImageService $userImageService)
    {
        $this->userImageService = $userImageService;
    }

    public function __invoke(Request $request)
    {
        $usuario = $this->userImageService->Upload($request);
        return $usuario;
    }
}
