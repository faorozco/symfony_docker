<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EntidadCampo
{

    public function __construct(EntidadCampoService $entidadCampoService)
    {
        $this->entidadCampoService = $entidadCampoService;
    }

    public function __invoke(Request $request)
    {
        return $this->entidadCampoService->get();
    }
}
