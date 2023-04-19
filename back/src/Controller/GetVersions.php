<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class GetVersions
{
    public function __construct(GetVersionsService $getVersionsService)
    {
        $this->getVersionsService = $getVersionsService;
    }

    public function __invoke(Request $request)
    {
        return $this->getVersionsService->get();
    }
}
