<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ExportCustomMasterQuery
{
    public function __construct(ExportCustomMasterQueryService $exportCustomMasterQueryService)
    {
        $this->exportCustomMasterQueryService = $exportCustomMasterQueryService;
    }

    public function __invoke(Request $request)
    {
        $nombreArchivo = $this->exportCustomMasterQueryService->export($request);
        $schema = $request->server->get("SYMFONY_DEFAULT_ROUTE_SCHEME");
        if ($schema == "") {
            $schema = "https";
        }
        $baseurl = $schema . '://' . $request->getHttpHost() . $request->getBasePath();

        return array("response" => array("location" => $baseurl . "/tmp/" . $nombreArchivo, "nombre" => $nombreArchivo));
    }
}
