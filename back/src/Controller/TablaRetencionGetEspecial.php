<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionGetEspecial
{
    private $_tablaRetencionGetEspecialService;
    private $requestStack;

    public function __construct(TablaRetencionGetEspecialService $tablaRetencionGetEspecialService)
    {
        $this->tablaRetencionGetEspecialService = $tablaRetencionGetEspecialService;

    }

    public function __invoke(Request $request)
    {
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $order = $request->query->get('order');
        $items_per_page = $request->attributes->get('_items_per_page');
        $tablaRetencionGetEspecial = $this->tablaRetencionGetEspecialService->get($page, $query, $order, $items_per_page);
        return $tablaRetencionGetEspecial;
    }
}
