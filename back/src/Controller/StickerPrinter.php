<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class StickerPrinter
{
    private $_QRBarCodeViewerService;

    public function __construct(StickerPrinterService $stickerPrinterService)
    {
        $this->stickerPrinterService = $stickerPrinterService;
    }

    public function __invoke(Request $request)
    {
        return $this->stickerPrinterService->Get($request);
    }
}
