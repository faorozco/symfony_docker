<?php
// api/src/Controller/NewUser.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class FormFieldLister
{

    public function __construct(FormFieldListerService $formFieldListerService)
    {
        $this->formFieldListerService = $formFieldListerService;
    }

    public function __invoke(Request $request)
    {
        if (null !== $request->query->get('page')) {
            $page = $request->query->get('page');
        } else {
            $page = "";
        }
        if (null !== $request->query->get('query')) {
            $query = $request->query->get('query');
        } else { 
            $query = "";
        }

        // $order = $request->query->get('order');
        if (null !== $request->query->get('_items_per_page')) {
            $items_per_page = $request->attributes->get('_items_per_page');
        }else{
            $items_per_page=200;
        }

        if (null !== $request->attributes->get('id')) {
            $id = $request->attributes->get('id');
        }else{
            $id = 0;
        }

        return $this->formFieldListerService->get($page, $query, $items_per_page, $id);
    }
}
