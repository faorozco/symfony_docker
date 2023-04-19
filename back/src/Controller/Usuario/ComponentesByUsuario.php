<?php
// api/src/Controller/ComponentesByUsuario.php

namespace App\Controller\Usuario;

use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;


class ComponentesByUsuario
{
    private $_componentesByUsuarioService;

    public function __construct(ComponentesByUsuarioService $componentesByUsuarioService)
    {
        $this->componentesByUsuarioService = $componentesByUsuarioService;
    }

    public function __invoke(Request $request)
    {

        $usuario=$this->componentesByUsuarioService->find($request);    
        return $usuario;
    }
}