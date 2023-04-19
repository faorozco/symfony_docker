<?php

namespace App\Controller;


class LogoutController
{
    public function __construct(LogoutService $logoutService)
    {
        $this->logoutService = $logoutService;
    }

    public function __invoke()
    {
        return $this->logoutService->logout();
    }
}