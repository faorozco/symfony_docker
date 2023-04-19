<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Async;

use Symfony\Component\HttpFoundation\Request;


class SaveSharedAsync
{
    public function __construct(SaveSharedAsyncService $saveSharedAsyncService)
    {
        $this->SaveSharedAsyncService = $saveSharedAsyncService;
    }

    public function __invoke(Request $request)
    {        
        return $this->SaveSharedAsyncService->save($request);
    }
}
