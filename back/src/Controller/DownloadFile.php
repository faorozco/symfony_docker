<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DownloadFile
{
    private $_downloadFileService;

    public function __construct(DownloadFileService $_downloadFileService)
    {
        $this->_downloadFileService = $_downloadFileService;
    }

    public function __invoke(Request $request)
    {
        $result = $this->_downloadFileService->Get($request);
        return $result;
    }
}
