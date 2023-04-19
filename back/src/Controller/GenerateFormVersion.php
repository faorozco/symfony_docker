<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class GenerateFormVersion
{
    public function __construct(GenerateFormVersionService $generateFormVersionService)
    {
        $this->generateFormVersionService = $generateFormVersionService;
    }

    public function __invoke(Request $request)
    {
        return $this->generateFormVersionService->generate($request);
    }
}
