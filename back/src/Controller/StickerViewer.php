<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class StickerViewer
{
    private $_QRBarCodeViewerService;

    public function __construct(StickerViewerService $stickerViewerService)
    {
        $this->stickerViewerService = $stickerViewerService;
    }

    public function __invoke(Request $request)
    {
        return $this->stickerViewerService->Get($request);
    }
}
