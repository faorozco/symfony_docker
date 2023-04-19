<?php

namespace App\Controller\Paso;

use Symfony\Component\HttpFoundation\Request;

class DeletePasos
{

    public function __construct(DeletePasosService $deletePasosService)
    {
        $this->deletePasosService = $deletePasosService;
    }

    public function __invoke(Request $request)
    {
        return $this->deletePasosService->Delete($request);
    }
}
