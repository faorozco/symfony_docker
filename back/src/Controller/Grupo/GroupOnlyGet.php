<?php

namespace App\Controller\Grupo;

use Symfony\Component\HttpFoundation\Request;

class GroupOnlyGet
{

    public function __construct(GroupOnlyGetService $GroupOnlyGetService)
    {
        $this->GroupOnlyGetService = $GroupOnlyGetService;
    }

    public function __invoke(Request $request)
    {
        return $this->GroupOnlyGetService->get($request);
    }
}
