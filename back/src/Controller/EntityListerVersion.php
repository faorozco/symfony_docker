<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EntityListerVersion
{

    public function __construct(EntityListerVersionService $entityListerVersionService)
    {
        $this->entityListerService = $entityListerVersionService;
    }

    public function __invoke(Request $request)
    {
        $page = $request->query->get('page');
        $query = $request->query->get('query');
        // $order = $request->query->get('order');
        $items_per_page = $request->attributes->get('_items_per_page');
        $id = $request->attributes->get('id');
        return $this->entityListerService->List($page, $query, $items_per_page, $id);
    }
}
