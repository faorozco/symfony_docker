<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class AuditoriaRegistro
{

    public function __construct(AuditoriaRegistroService $auditoriaRegistroService)
    {
        $this->auditoriaRegistroService = $auditoriaRegistroService;
    }

    public function __invoke(Request $request)
    {
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        $items_per_page = $request->attributes->get('_items_per_page');
        $id = $request->attributes->get('id');
        return $this->auditoriaRegistroService->list($page, $query, $items_per_page, $id);
    }
}
