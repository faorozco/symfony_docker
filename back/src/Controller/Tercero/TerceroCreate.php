<?php

namespace App\Controller\Tercero;

use Symfony\Component\HttpFoundation\Request;

class TerceroCreate
{

    public function __construct(TerceroService $terceroService)
    {
        $this->terceroService = $terceroService;
    }

    public function __invoke(Request $request)
    {
        $tercero = json_decode($request->getContent());
        return $this->terceroService->create($tercero);
    }
}
