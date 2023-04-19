<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class Listas
{

    public function __construct(ListasService $listasService)
    {
        $this->listasService = $listasService;
    }

    public function __invoke(Request $request)
    {
        return $this->listasService->get($request);
    }
}
