<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ArchivoRegistro
{
    public function __construct(ArchivoRegistroService $archivoRegistroService)
    {
        $this->archivoRegistroService = $archivoRegistroService;
    }

    public function __invoke(Request $request)
    {
        return $this->archivoRegistroService->save($request);
    }
}
