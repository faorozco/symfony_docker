<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;


class TemplateCreate
{
    private $_templateCreateService;

    public function __construct(TemplateCreateService $templateCreateService)
    {
        $this->templateCreateService = $templateCreateService;
    }

    public function __invoke(Request $request)
    {

        return $this->templateCreateService->create($request);        
    }
}