<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class AprobarVistoBueno
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $ejecucionPasoId = $request->attributes->get("id");
        $state = $request->query->get("state");
        $comment = $request->query->get("comment");
        return $this->ejecucionPasoService->aprobarVistoBueno($ejecucionPasoId, $state, $comment);
    }
}
