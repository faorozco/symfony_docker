<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormatoSave
{
    private $_formatoSaveService;

    public function __construct(FormatoSaveService $formatoSaveService)
    {
        $this->formatoSaveService = $formatoSaveService;
    }

    public function __invoke(Request $request)
    {
        return $this->formatoSaveService->save($request);
    }
}
