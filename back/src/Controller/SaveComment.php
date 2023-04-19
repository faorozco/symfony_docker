<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class SaveComment
{
    public function __construct(SaveCommentService $saveCommentService)
    {
        $this->saveCommentService = $saveCommentService;
    }

    public function __invoke(Request $request)
    {        
        return $this->saveCommentService->save($request);
    }
}
