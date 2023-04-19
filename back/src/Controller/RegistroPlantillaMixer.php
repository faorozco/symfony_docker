<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistroPlantillaMixer
{
    private $_QRBarCodeViewerService;

    public function __construct(RegistroPlantillaMixerService $registroPlantillaMixerService)
    {
        $this->registroPlantillaMixerService = $registroPlantillaMixerService;
    }

    public function __invoke(Request $request)
    {
        return $this->registroPlantillaMixerService->Mix($request);
    }
}
