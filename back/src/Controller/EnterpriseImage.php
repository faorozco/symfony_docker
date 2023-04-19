<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EnterpriseImage
{
    private $_enterpriseImageService;

    public function __construct(EnterpriseImageService $enterpriseImageService)
    {
        $this->enterpriseImageService = $enterpriseImageService;
    }

    public function __invoke(Request $request)
    {
        $usuario = $this->enterpriseImageService->Upload($request);
        return $usuario;
    }
}
