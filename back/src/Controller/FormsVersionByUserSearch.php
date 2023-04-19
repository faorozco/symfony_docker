<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormsVersionByUserSearch
{

    public function __construct(FormsVersionByUserSearchService $formsVersionByUserSearchService)
    {
        $this->formsVersionByUserSearchService = $formsVersionByUserSearchService;
    }

    public function __invoke(Request $request)
    {
        $query = $request->query->get('query');
        return $this->formsVersionByUserSearchService->get($query);
    }
}
