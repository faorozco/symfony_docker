<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Formulario;

use Symfony\Component\HttpFoundation\Request;

class FormList
{
    public function __construct(FormularioService $formularioService)
    {
        $this->formularioService = $formularioService;
    }
    public function __invoke(Request $request)
    {
        $filtro = $request->query->get('query');
        $paginaActual = $request->query->get('page');
        $mostrarInactivo = $request->query->get('mostrarInactivos');
        $size = $request->query->get('size');
        return $this->formularioService->list($filtro,$paginaActual,$mostrarInactivo,$size);
    }
}
