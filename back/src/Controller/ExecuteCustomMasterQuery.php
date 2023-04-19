<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ExecuteCustomMasterQuery
{
    public function __construct(ExecuteCustomMasterQueryService $executeCustomMasterQueryService)
    {
        $this->executeCustomMasterQueryService = $executeCustomMasterQueryService;
    }

    public function __invoke(Request $request)
    {
        return $this->executeCustomMasterQueryService->execute($request);
    }
}
