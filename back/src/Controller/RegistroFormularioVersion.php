<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegistroFormularioVersion
{
    private $_registroFormularioVersion;

    public function __construct(RegistroFormularioVersionService $registroFormularioServiceVersion)
    {
        $this->registroFormularioServiceVersion = $registroFormularioServiceVersion;
    }

    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent());
        $formularioVersionId = $data->{"formularioVersion"};
        $estadoId = $data->{"estadoId"};

        $ejecucionFlujoId = null;
        if(isset($data->{"ejecucionFlujoId"})) {
            $ejecucionFlujoId = $data->{"ejecucionFlujoId"};
        }

        $tipoCorrespondencia = null;
        if(isset($data->{"tipo_correspondencia"})) {
            $tipoCorrespondencia = $data->{"tipo_correspondencia"};
        }

        $ejecucionPasoId = null;
        if (isset($data->{"ejecucionPasoId"})) {
            $ejecucionPasoId = $data->{"ejecucionPasoId"};
        }

        $registroId = null;
        if (isset($data->{"registroId"})) {
            $registroId = $data->{"registroId"};
        }

        $registros = $data->{"registros"};
        
        $registroFormularioVersion = $this->registroFormularioServiceVersion->save(
            $formularioVersionId,
            $estadoId,
            $ejecucionFlujoId,
            $tipoCorrespondencia,
            $ejecucionPasoId,
            $registros,
            $registroId
        );
        return $registroFormularioVersion;
    }
}
