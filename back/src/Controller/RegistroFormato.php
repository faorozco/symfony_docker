<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistroFormato
{

    public function __construct(RegistroFormatoService $registroFormatoService)
    {
        $this->registroFormatoService = $registroFormatoService;
    }

    public function __invoke(Request $request)
    {
        
        $formatos = $this->registroFormatoService->get($request);
        return $formatos;
    }
}
