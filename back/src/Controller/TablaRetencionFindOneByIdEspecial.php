<?php
// api/src/Controller/EstructuraDocumentalByNode.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class TablaRetencionFindOneByIdEspecial
{
    private $_tablaRetencionFindOneByIdEspecial;

    public function __construct(TablaRetencionFindOneByIdEspecialService $tablaRetencionFindOneByIdEspecialService)
    {
        $this->tablaRetencionFindOneByIdEspecialService = $tablaRetencionFindOneByIdEspecialService;

    }

    public function __invoke(Request $request)
    {
        $id = $request->attributes->get('id');
        $tablaRetencionDto = $this->tablaRetencionFindOneByIdEspecialService->find($id);
        return $tablaRetencionDto;
    }
}
