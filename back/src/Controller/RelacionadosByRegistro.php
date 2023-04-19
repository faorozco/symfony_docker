<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RelacionadosByRegistro
{
    private $_QRBarCodeViewerService;

    public function __construct(RelacionadosByRegistroService $relacionadosByRegistroService)
    {
        $this->relacionadosByRegistroService = $relacionadosByRegistroService;
    }

    public function __invoke(Request $request)
    {
        $registroId = $request->attributes->get("id");
        $queryString = $request->query->get("query");
        $page = $request->query->get("page");
        $itemsPerPage = 20;
        $estado = 1;
        $orderBy = 'DESC';
        return $this->relacionadosByRegistroService->Get($registroId,$queryString, $page, $itemsPerPage, $estado, $orderBy);
    }
}
