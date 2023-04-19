<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ConsultaMaestraSave
{
    public function __construct(ConsultaMaestraSaveService $consultaMaestraSaveService)
    {
        $this->consultaMaestraSaveService = $consultaMaestraSaveService;
    }

    public function __invoke(Request $request)
    {
        return $this->consultaMaestraSaveService->save($request);
    }
}
