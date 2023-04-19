<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class InactivateNode
{
    public function __construct(InactivateNodeService $inactivateNodeService)
    {
        $this->inactivateNodeService = $inactivateNodeService;
    }

    public function __invoke(Request $request)
    {
        return $this->inactivateNodeService->inactivate($request);
    }
}
