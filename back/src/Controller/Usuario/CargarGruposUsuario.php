<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use Symfony\Component\HttpFoundation\Request;

class CargarGruposUsuario
{

    public function __construct(CargarGruposUsuarioService $cargarGruposUsuarioService)
    {
        $this->cargarGruposUsuarioService = $cargarGruposUsuarioService;
    }

    public function __invoke(Request $request)
    {
        return $this->cargarGruposUsuarioService->get($request);
    }
}
