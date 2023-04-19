<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class SaveNotificacion
{
    public function __construct(SaveNotificacionService $saveNotificacionService)
    {
        $this->saveNotificacionService = $saveNotificacionService;
    }

    public function __invoke(Request $request, KernelInterface $kernel)
    {
        return $this->saveNotificacionService->save($request, $kernel);
    }
}
