<?php

namespace App\Controller\Contacto;

use Symfony\Component\HttpFoundation\Request;

class ContactoCreate
{

    public function __construct(ContactoService $ContactoService)
    {
        $this->ContactoService = $ContactoService;
    }

    public function __invoke(Request $request)
    {
        $Contacto = json_decode($request->getContent());
        return $this->ContactoService->create($Contacto);
    }
}
