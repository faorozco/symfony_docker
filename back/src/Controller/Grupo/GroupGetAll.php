<?php

namespace App\Controller\Grupo;

use Symfony\Component\HttpFoundation\Request;

class GroupGetAll
{

    public function __construct(GroupGetAllService $GroupGetAllService)
    {
        $this->GroupGetAllService = $GroupGetAllService;
    }

    public function __invoke(Request $request)
    {
        return $this->GroupGetAllService->get($request);
    }
}
