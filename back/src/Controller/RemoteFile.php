<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RemoteFile
{

    public function __construct(RemoteFileService $remoteFileService)
    {
        $this->remoteFileService = $remoteFileService;
    }

    public function __invoke(Request $request)
    {
        return $this->remoteFileService->get($request);
    }
}
