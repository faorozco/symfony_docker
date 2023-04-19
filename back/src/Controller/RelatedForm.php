<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RelatedForm
{
    public function __construct(RelatedFormService $relatedFormService)
    {
        $this->relatedFormService = $relatedFormService;
    }

    public function __invoke(Request $request)
    {
        return $this->relatedFormService->get($request);
    }
}
