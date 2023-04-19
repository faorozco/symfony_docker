<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class InactivateWorkFlow
{
    public function __construct(InactivateWorkFlowService $inactivateWorkFlowService)
    {
        $this->inactivateWorkFlowService = $inactivateWorkFlowService;
    }

    public function __invoke(Request $request)
    {
        return $this->inactivateWorkFlowService->inactivate($request);
    }
}
