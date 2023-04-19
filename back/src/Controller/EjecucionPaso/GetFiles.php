<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class GetFiles
{

    public function __construct(GetFileService $getFileService)
    {
        $this->getFileService = $getFileService;
    }

    public function __invoke(Request $request)
    {
        return $this->getFileService->get($request);
    }
}
