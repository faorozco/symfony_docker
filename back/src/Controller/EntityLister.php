<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class EntityLister
{

    public function __construct(EntityListerService $stickerViewerService)
    {
        $this->entityListerService = $stickerViewerService;
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
