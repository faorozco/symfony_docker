<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistroArchivo
{
    private $_registroArchivo;

    public function __construct(RegistroArchivoService $registroArchivoService)
    {
        $this->registroArchivoService = $registroArchivoService;
    }

    public function __invoke(Request $request)
    {
        
        $registroArchivo = $this->registroArchivoService->get($request);
        return $registroArchivo;
    }
}
