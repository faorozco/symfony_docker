<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ListaSave
{

    public function __construct(ListaSaveService $listaSaveService)
    {
        $this->listaSaveService = $listaSaveService;
    }

    public function __invoke(Request $request)
    {
        return $this->listaSaveService->save($request);
    }
}
