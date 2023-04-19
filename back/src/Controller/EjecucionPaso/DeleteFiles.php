<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class DeleteFiles
{

    public function __construct(DeleteFileService $DeleteFileService)
    {
        $this->DeleteFileService = $DeleteFileService;
    }

    public function __invoke(Request $request)
    {
        return $this->DeleteFileService->delete($request);
    }
}
