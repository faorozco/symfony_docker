<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ActiveLicense
{

    public function __construct(ActiveLicenseService $activeLicenseService)
    {
        $this->activeLicenseService = $activeLicenseService;
    }

    public function __invoke(Request $request)
    {
        return $this->activeLicenseService->verified($request);
    }
}
