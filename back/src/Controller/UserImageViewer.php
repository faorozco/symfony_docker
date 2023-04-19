<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserImageViewer
{
    private $_userImageViewerService;

    public function __construct(UserImageViewerService $userImageViewerService)
    {
        $this->userImageViewerService = $userImageViewerService;
    }

    public function __invoke(Request $request)
    {
        $usuario = $this->userImageViewerService->Get($request);
        return $usuario;
    }
}
