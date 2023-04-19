<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CamposFormularioUpdate
{
    private $_QRBarCodeViewerService;

    public function __construct(CamposFormularioUpdateService $camposFormularioUpdateService)
    {
        $this->camposFormularioUpdateService = $camposFormularioUpdateService;
    }

    public function __invoke(Request $request)
    {

        $respuestas = json_decode($request->getContent());
        $ejecucionPasoId = $request->query->get('ejecucionPasoId');

        return $this->camposFormularioUpdateService->Get($respuestas, $ejecucionPasoId);
    }
}
