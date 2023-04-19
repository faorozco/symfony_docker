<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ActivateWorkFlow
{
    public function __construct(ActivateWorkFlowService $activateWorkFlowService)
    {
        $this->activateWorkFlowService = $activateWorkFlowService;
    }

    public function __invoke(Request $request)
    {
        return $this->activateWorkFlowService->activate($request);
    }
}
