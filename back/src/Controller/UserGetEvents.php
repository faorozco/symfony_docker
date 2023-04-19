<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserGetEvents
{

    public function __construct(UserGetEventsService $UserGetEventService)
    {
        $this->UserGetEventService = $UserGetEventService;
    }

    public function __invoke(Request $request)
    {
        return $this->UserGetEventService->post($request);
    }
}
