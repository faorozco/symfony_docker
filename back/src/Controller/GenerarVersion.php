<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class GenerarVersion
{
    public function __construct(GenerarVersionService $generarVersionService)
    {
        $this->generarVersionService = $generarVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->generarVersionService->generate($request);
    }
}
