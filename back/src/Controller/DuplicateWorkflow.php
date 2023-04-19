<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DuplicateWorkflow
{

    public function __construct(DuplicateWorkflowService $duplicateWorkflowService)
    {
        $this->duplicateWorkflowService = $duplicateWorkflowService;
    }

    public function __invoke(Request $request)
    {
        return $this->duplicateWorkflowService->duplicateWorkFlow($request);
    }
}
