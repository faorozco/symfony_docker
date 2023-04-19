<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class HistoricoArchivo
{
    public function __construct(HistoricoArchivoService $historicoArchivoService)
    {
        $this->historicoArchivoService = $historicoArchivoService;
    }

    public function __invoke(Request $request)
    {
        return $this->historicoArchivoService->save($request);
    }
}