<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Comments;

use Symfony\Component\HttpFoundation\Request;

class GetComments
{

    public function __construct(GetCommentsService $getCommentsService)
    {
        $this->getCommentsService = $getCommentsService;
    }

    public function __invoke(Request $request)
    {
        return $this->getCommentsService->get($request);
    }
}
