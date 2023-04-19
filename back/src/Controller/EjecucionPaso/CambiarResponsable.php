<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class CambiarResponsable
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionPasoId = $request->attributes->get("id");
        $usuarioId = $request->query->get("usuarioId");
        $comment = $request->query->get("comment");
        return $this->ejecucionPasoService->cambiarResponsable($ejecucionPasoId, $usuarioId, $comment);
    }
}
