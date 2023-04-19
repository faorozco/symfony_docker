<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class DuplicateCodigoDirectorio
{
    public function __construct(DuplicateCodigoDirectorioService $duplicateCodigoDirectorioService)
    {
        $this->duplicateCodigoDirectorioService = $duplicateCodigoDirectorioService;
    }

    public function __invoke(Request $request)
    {
        return $this->duplicateCodigoDirectorioService->check($request);
    }
}
