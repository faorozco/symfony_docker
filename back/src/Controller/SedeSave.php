<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class SedeSave
{
    private $_sedeSaveService;

    public function __construct(SedeSaveService $sedeSaveService)
    {
        $this->sedeSaveService = $sedeSaveService;
    }

    public function __invoke(Request $request)
    {

        $sede = $this->sedeSaveService->save($request);
        return $sede;
    }
}
