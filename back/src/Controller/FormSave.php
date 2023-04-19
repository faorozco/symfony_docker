<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormSave
{

    public function __construct(FormSaveService $formSaveService)
    {
        $this->formSaveService = $formSaveService;
    }

    public function __invoke(Request $request)
    {
        return $this->formSaveService->save($request);
    }
}
