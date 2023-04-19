<?php

namespace App\Controller\FlujoTrabajoVersion;

use Symfony\Component\HttpFoundation\Request;

class FlujoTrabajoVersionAssociate
{

    public function __construct(FlujoTrabajoVersionAssociateService $flujoTrabajoVersionAssociateService)
    {
        $this->flujoTrabajoVersionAssociateService = $flujoTrabajoVersionAssociateService;
    }

    public function __invoke(Request $request)
    {
        return $this->flujoTrabajoVersionAssociateService->post($request);
    }
}
