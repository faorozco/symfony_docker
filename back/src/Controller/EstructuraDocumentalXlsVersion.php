<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EstructuraDocumentalXlsVersion
{

    public function __construct(EstructuraDocumentalXlsVersionService $estructuraDocumentalXlsVersionService) {
        $this->estructuraDocumentalXlsVersionService = $estructuraDocumentalXlsVersionService;
    }

    public function __invoke(Request $request)
    {

        $nodo = $request->attributes->get("nodo");
        $version = $request->attributes->get("version");

        if ($nodo == '-1') {
            $nodo = $_ENV["CODIGO_DIRECTORIO_FONDO"];
        }

        $nodo = $_ENV["CODIGO_DIRECTORIO_FONDO"];

        $nombreArchivo = $this->estructuraDocumentalXlsVersionService->get($nodo, $version);
        $schema = $request->server->get("SYMFONY_DEFAULT_ROUTE_SCHEME");
        if ($schema == "") {
            $schema = "https";
        }
        $baseurl = $schema . '://' . $request->getHttpHost() . $request->getBasePath();

        return array("response" => array("location" => $baseurl . "/tmp/" . $nombreArchivo, "nombre" => $nombreArchivo));

    }
}
