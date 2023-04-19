<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CurrentVersion
{
    public function __construct(CurrentVersionService $currentVersionService)
    {
        $this->currentVersionService = $currentVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->currentVersionService->get();
    }
}
