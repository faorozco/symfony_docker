<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class OpcionFormMany
{

    public function __construct(OpcionFormManyService $opcionFormManyService)
    {
        $this->opcionFormManyService = $opcionFormManyService;
    }

    public function __invoke(Request $request)
    {
        $this->opcionFormManyService->get($request);

        return array('response' => array('message' => 'Las opciones del formulario se guardaron correctamente.'));
    }
}
