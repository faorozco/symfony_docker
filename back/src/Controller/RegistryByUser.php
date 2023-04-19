<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistryByUser
{
    public function __construct(RegistryByUserService $registryByUserService)
    {
        $this->registryByUserService = $registryByUserService;
    }

    public function __invoke(Request $request)
    {
        return $this->registryByUserService->Get($request);
    }
}
