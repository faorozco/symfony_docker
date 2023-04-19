<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TipoDocumental
{
    public function __construct(TipoDocumentalService $tipoDocumentalService)
    {
        $this->tipoDocumentalService = $tipoDocumentalService;
    }

    public function __invoke(Request $request)
    {
        return $this->tipoDocumentalService->get($request);
    }
}
