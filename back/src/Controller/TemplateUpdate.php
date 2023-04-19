<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;


class TemplateUpdate
{
    private $_templateCreateService;

    public function __construct(TemplateUpdateService $templateUpdateService)
    {
        $this->templateUpdateService = $templateUpdateService;
    }

    public function __invoke(Request $request)
    {

        return $this->templateUpdateService->update($request);        
    }
}