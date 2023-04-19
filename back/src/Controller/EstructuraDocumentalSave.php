<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalSave
{
    public function __construct(EstructuraDocumentalSaveService $estructuraDocumentalSaveService)
    {
        $this->estructuraDocumentalSaveService = $estructuraDocumentalSaveService;
    }

    public function __invoke(Request $request)
    {

        $node = json_decode($request->getContent());

        return $this->estructuraDocumentalSaveService->save($node);
    }
}
