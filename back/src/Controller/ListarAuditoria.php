<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ListarAuditoria
{

    public function __construct(ListarAuditoriaService $listarAuditoriaService)
    {
        $this->listarAuditoriaService = $listarAuditoriaService;
    }

    public function __invoke(Request $request)
    {
        $items_per_page = $request->attributes->get('_items_per_page');        
        return $this->listarAuditoriaService->list($items_per_page, $request);
    }
}
