<?php
// api/src/Controller/NewUser.php

namespace App\Controller\PasoEvento;

use Symfony\Component\HttpFoundation\Request;

class CreateEvents
{
    public function __construct(CreateEventService $createEventService)
    {
        $this->createEventService = $createEventService;
    }

    public function __invoke(Request $request)
    {
        return $this->createEventService->Create($request);
    }
}
