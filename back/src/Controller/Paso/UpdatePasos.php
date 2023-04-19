<?php

namespace App\Controller\Paso;

use Symfony\Component\HttpFoundation\Request;

class UpdatePasos
{

    public function __construct(UpdatePasosService $updatePasosService)
    {
        $this->updatePasosService = $updatePasosService;
    }

    public function __invoke(Request $request)
    {
        return $this->updatePasosService->update($request);
    }
}
