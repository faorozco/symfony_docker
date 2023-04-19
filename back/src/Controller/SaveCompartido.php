<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class SaveCompartido
{
    public function __construct(SaveCompartidoService $saveCompartidoService)
    {
        $this->saveCompartidoService = $saveCompartidoService;
    }

    public function __invoke(Request $request, KernelInterface $kernel)
    {        
        return $this->saveCompartidoService->save($request, $kernel);
    }
}
