<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ExecuteMasterQuery
{
    public function __construct(ExecuteMasterQueryService $executeMasterQueryService)
    {
        $this->executeMasterQueryService = $executeMasterQueryService;
    }

    public function __invoke(Request $request)
    {
        return $this->executeMasterQueryService->execute($request);
    }
}
