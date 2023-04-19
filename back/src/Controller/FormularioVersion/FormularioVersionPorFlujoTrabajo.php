<?php
// api/src/Controller/NewUser.php

namespace App\Controller\FormularioVersion;

use Symfony\Component\HttpFoundation\Request;

class FormularioVersionPorFlujoTrabajo
{

    public function __construct(FormularioVersionService $formularioVersionService)
    {
        $this->formularioVersionService = $formularioVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->formularioVersionService->getPorFlujoTrabajoVersion($request);
    }
}
