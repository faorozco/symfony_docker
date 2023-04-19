<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RetentionTableCreate
{
    private $_retentionTableCreateService;

    public function __construct(RetentionTableCreateService $retentionTableCreateService)
    {
        $this->retentionTableCreateService = $retentionTableCreateService;
    }

    public function __invoke(Request $request)
    {

        return $this->retentionTableCreateService->create($request);        
    }
}