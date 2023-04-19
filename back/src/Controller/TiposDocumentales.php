<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TiposDocumentales
{
    public function __construct(TiposDocumentalesService $tiposDocumentalesService)
    {
        $this->tiposDocumentalesService = $tiposDocumentalesService;
    }

    public function __invoke(Request $request)
    {
        return $this->tiposDocumentalesService->get($request);
    }
}
