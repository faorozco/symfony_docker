<?php
// api/src/Controller/NewUser.php

namespace App\Controller\PasoEvento;

use Symfony\Component\HttpFoundation\Request;

class ConfigEvents
{

    public function __construct(ConfigEventsService $configEventsService)
    {
        $this->configEventsService = $configEventsService;
    }

    public function __invoke(Request $request)
    {
        return $this->configEventsService->Search($request);
    }
}
