<?php
// api/src/Controller/EstructuraDocumentalVersionByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalVersionByNode
{
    private $_estructuraDocumentalVersionByNodeService;

    public function __construct(EstructuraDocumentalVersionByNodeService $estructuraDocumentalVersionByNodeService)
    {
        $this->estructuraDocumentalVersionByNodeService = $estructuraDocumentalVersionByNodeService;
    }

    public function __invoke(Request $request)
    {
        $node = $request->query->get('node');
        if (null !== $request->query->get('mostrarpeso')) {
            $mostrarPeso = $request->query->get('mostrarpeso');
        } else {
            $mostrarPeso = null;
        }
        $estructuraDocumentalVersion = $this->estructuraDocumentalVersionByNodeService->generarEstructuraDocumentalVersion($node, $mostrarPeso);
        return $estructuraDocumentalVersion;
    }
}
