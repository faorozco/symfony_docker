<?php

namespace App\Controller\EjecucionPaso;

use Symfony\Component\HttpFoundation\Request;

class DevolucionStep
{

    public function __construct(EjecucionPasoService $ejecucionPasoService)
    {
        $this->ejecucionPasoService = $ejecucionPasoService;
    }

    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent());
        if(
            isset($data->{'pasoActual'}) &&
            isset($data->{'userId'})  &&
            isset($data->{'accion'}) &&
            isset($data->{'PasoDevolucion'}) &&
            isset($data->{'comentario'})
            ){
                $pasoActual = $data->{'pasoActual'};
                $userId = $data->{'userId'};
                $accion = $data->{'accion'};
                $PasoDevolucion = $data->{'PasoDevolucion'};
                $badDevolucion = false;
                $comentario = $data->{'comentario'};
            }
        else{
            $badDevolucion = true;
        }    
        return $this->ejecucionPasoService->returnStep($pasoActual, $userId, $accion, $PasoDevolucion, $badDevolucion,$comentario);
    }
}
