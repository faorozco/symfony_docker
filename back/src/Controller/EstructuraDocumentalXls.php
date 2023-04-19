<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalXls
{

    public function __construct(EstructuraDocumentalXlsService $estructuraDocumentalXlsService) {
        $this->estructuraDocumentalXlsService = $estructuraDocumentalXlsService;
    }

    public function __invoke(Request $request)
    {

        $nodo = $request->attributes->get("nodo");

        if ($nodo == '-1') {
            $nodo = $_ENV["CODIGO_DIRECTORIO_FONDO"];
        }

        $nombreArchivo = $this->estructuraDocumentalXlsService->get($nodo);

        $schema = $request->server->get("SYMFONY_DEFAULT_ROUTE_SCHEME");
        if ($schema == "") {
            $schema = "https";
        }
        $baseurl = $schema . '://' . $request->getHttpHost() . $request->getBasePath();

        return array("response" => array("location" => $baseurl . "/tmp/" . $nombreArchivo, "nombre" => $nombreArchivo));

    }
}
