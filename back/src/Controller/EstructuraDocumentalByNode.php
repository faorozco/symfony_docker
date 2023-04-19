<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalByNode
{
    private $_estructuraDocumentalByNodeService;

    public function __construct(EstructuraDocumentalByNodeService $estructuraDocumentalByNodeService)
    {
        $this->estructuraDocumentalByNodeService = $estructuraDocumentalByNodeService;
    }

    public function __invoke(Request $request)
    {
        $node = $request->query->get('node');
        if (null !== $request->query->get('mostrarpeso')) {
            $mostrarPeso = $request->query->get('mostrarpeso');
        } else {
            $mostrarPeso = null;
        }
        $estructuraDocumental = $this->estructuraDocumentalByNodeService->generarEstructuraDocumental($node, $mostrarPeso);
        return $estructuraDocumental;
    }
}
