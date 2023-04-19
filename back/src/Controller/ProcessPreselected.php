<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ProcessPreselected
{
    private $_QRBarCodeViewerService;

    public function __construct(ProcessPreselectedService $processPreselectedService)
    {
        $this->processPreselectedService = $processPreselectedService;
    }

    public function __invoke(Request $request)
    {
        return $this->processPreselectedService->process($request);
    }
}
