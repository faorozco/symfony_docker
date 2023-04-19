<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class NotificadosByRegistro
{
    public function __construct(NotificadosByRegistroService $notificadosByRegistroService)
    {
        $this->notificadosByRegistroService = $notificadosByRegistroService;
    }

    public function __invoke(Request $request)
    {
        return $this->notificadosByRegistroService->Get($request);
    }
}
