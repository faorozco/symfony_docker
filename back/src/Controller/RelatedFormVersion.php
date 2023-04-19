<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RelatedFormVersion
{
    public function __construct(RelatedFormVersionService $relatedFormVersionService)
    {
        $this->relatedFormVersionService = $relatedFormVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->relatedFormVersionService->get($request);
    }
}
