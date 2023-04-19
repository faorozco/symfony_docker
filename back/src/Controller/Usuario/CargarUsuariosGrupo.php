<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Usuario;

use Symfony\Component\HttpFoundation\Request;

class CargarUsuariosGrupo
{

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function __invoke(Request $request)
    {
        return $this->usuarioService->cargarUsuariosGrupo($request);
    }
}
