<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistryHeader
{
    private $_QRBarCodeViewerService;

    public function __construct(RegistryHeaderService $registryHeaderService)
    {
        $this->registryHeaderService = $registryHeaderService;
    }

    public function __invoke(Request $request)
    {
        return $this->registryHeaderService->Get($request);
    }
}
