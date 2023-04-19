<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormatoGeneratePdf
{
    private $_formatoGeneratePdfService;

    public function __construct(FormatoGeneratePdfService $formatoPrinterService)
    {
        $this->formatoGeneratePdfService = $formatoPrinterService;
    }

    public function __invoke(Request $request)
    {
        return $this->formatoGeneratePdfService->Get($request);
    }
}
