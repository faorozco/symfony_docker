<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class VersionarArchivo
{
    public function __construct(VersionarArchivoService $versionarArchivoService)
    {
        $this->versionarArchivoService = $versionarArchivoService;
    }

    public function __invoke(Request $request)
    {
        return $this->versionarArchivoService->save($request);
    }
}