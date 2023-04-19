<?php

namespace App\Controller\Async;

use Symfony\Component\HttpFoundation\Request;


class SaveCompartidoAsync
{
    public function __construct(SaveCompartidoAsyncService $saveCompartidoAsyncService)
    {
        $this->SaveCompartidoAsyncService = $saveCompartidoAsyncService;
    }

    public function __invoke(Request $request)
    {        
        return $this->SaveCompartidoAsyncService->save($request);
    }
}
