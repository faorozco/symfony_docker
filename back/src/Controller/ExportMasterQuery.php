<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ExportMasterQuery
{
    public function __construct(ExportMasterQueryService $exportMasterQueryService)
    {
        $this->exportMasterQueryService = $exportMasterQueryService;
    }

    public function __invoke(Request $request)
    {
        return $this->exportMasterQueryService->export($request);
    }
}
