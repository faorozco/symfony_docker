<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistryCredentials
{
    public function __construct(RegistryCredentialsService $registryCredentialsService)
    {
        $this->registryCredentialsService = $registryCredentialsService;
    }

    public function __invoke(Request $request)
    {
        return $this->registryCredentialsService->Get($request);
    }
}
