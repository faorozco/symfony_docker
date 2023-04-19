<?php
// api/src/Controller/NewUser.php

namespace App\Controller\PasoEvento;

use Symfony\Component\HttpFoundation\Request;

class DeleteEvents
{

    public function __construct(DeleteEventsService $deleteEventsService)
    {
        $this->deleteEventsService = $deleteEventsService;
    }

    public function __invoke(Request $request)
    {
        return $this->deleteEventsService->Delete($request);
    }
}
