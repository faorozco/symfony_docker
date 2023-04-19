<?php

namespace App\Controller\EjecucionFlujo;

use Symfony\Component\HttpFoundation\Request;

class ConsultarPorUsuario
{

    public function __construct(EjecucionFlujoService $ejecucionFlujoService)
    {
        $this->ejecucionFlujoService = $ejecucionFlujoService;
    }

    public function __invoke(Request $request)
    {
        $filter = $request->query->get('filter');
        $order = $request->query->get('order');
        $page = $request->query->get('page');
        $size = $request->query->get('size');

        return $this->ejecucionFlujoService->consultarPorUsuario($filter, $order, $page, $size);
    }
}
