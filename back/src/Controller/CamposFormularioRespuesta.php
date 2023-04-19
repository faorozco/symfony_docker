<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CamposFormularioRespuesta
{
    private $_QRBarCodeViewerService;

    public function __construct(CamposFormularioRespuestaService $camposFormularioRespuestaService)
    {
        $this->camposFormularioRespuestaService = $camposFormularioRespuestaService;
    }
    

    public function __invoke(Request $request)
    {

        $id = $request->attributes->get("id");
        $registroId = $request->attributes->get("registro_id");
        $ejecucionPasoId = $request->query->get('ejecucionPasoId');

        return $this->camposFormularioRespuestaService->Get($id, $registroId, $ejecucionPasoId);
    }
}
