<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class NotifiedNotificacions
{
    private $_notifiedNotificacionsService;

    public function __construct(NotifiedNotificacionsService $notifiedNotificacionsService)
    {
        $this->notifiedNotificacionsService = $notifiedNotificacionsService;
    }

    public function __invoke(Request $request)
    {
        return $this->notifiedNotificacionsService->Get($request);
    }
}
