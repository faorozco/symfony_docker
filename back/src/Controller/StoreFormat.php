<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class StoreFormat
{
    private $_storeFormatService;

    public function __construct(StoreFormatService $storeFormatService)
    {
        $this->storeFormatService = $storeFormatService;
    }

    public function __invoke(Request $request)
    {
        return $this->storeFormatService->store($request);
    }
}
