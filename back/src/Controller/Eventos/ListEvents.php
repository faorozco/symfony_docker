<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Eventos;

use Symfony\Component\HttpFoundation\Request;

class ListEvents
{

    public function __construct(ListEventsService $listEventsService)
    {
        $this->listEventsService = $listEventsService;
    }

    public function __invoke(Request $request)
    {
        return $this->listEventsService->Duplicate($request);
    }
}
