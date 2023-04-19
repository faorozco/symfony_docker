<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EnterpriseImageViewer
{
    private $_enterpriseImageViewerService;

    public function __construct(EnterpriseImageViewerService $enterpriseImageViewerService)
    {
        $this->enterpriseImageViewerService = $enterpriseImageViewerService;
    }

    public function __invoke(Request $request)
    {
        $usuario = $this->enterpriseImageViewerService->Get($request);
        return $usuario;
    }
}
