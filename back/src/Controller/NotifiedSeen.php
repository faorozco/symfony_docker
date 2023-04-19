<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class NotifiedSeen
{
    private $_QRBarCodeViewerService;

    public function __construct(NotifiedSeenService $notifiedSeenService)
    {
        $this->notifiedSeenService = $notifiedSeenService;
    }

    public function __invoke(Request $request)
    {
        return $this->notifiedSeenService->Get($request);
    }
}
