<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class OpcionFormDelete
{

    public function __construct(OpcionFormDeleteService $opcionFormDeleteService)
    {
        $this->opcionFormDeleteService = $opcionFormDeleteService;
    }

    public function __invoke(Request $request)
    {

        $id = $request->attributes->get("id_form");

        $this->opcionFormDeleteService->get($id);

        return array('response' => array('message' => 'Las opciones del formulario se eliminaron correctamente.'));
    }
}
