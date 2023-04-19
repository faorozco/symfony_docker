<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Formulario;

use Symfony\Component\HttpFoundation\Request;

class FormCreate
{

    public function __construct(FormularioService $formularioService)
    {
        $this->formularioService = $formularioService;
    }

    public function __invoke(Request $request)
    {
        return $this->formularioService->create($request);
    }
}
