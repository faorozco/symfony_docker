<?php
// api/src/Controller/NewUser.php

namespace App\Controller\Comments;

use Symfony\Component\HttpFoundation\Request;

class SaveComments
{

    public function __construct(SaveCommentsService $saveCommentsService)
    {
        $this->saveCommentsService = $saveCommentsService;
    }

    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent());

        if(
        isset($data->{'comentario'}) && isset($data->{'ejecucionPasoId'}) &&
        isset($data->{'idUser'})   && isset($data->{'user'}) &&
        isset($data->{'nombreCompleto'}) && isset($data->{'typeComent'})
        ){
            return $this->saveCommentsService->create(
                $data->{'comentario'},
                $data->{'ejecucionPasoId'},
                $data->{'idUser'},
                $data->{'user'},
                $data->{'nombreCompleto'},
                $data->{'typeComent'}
            );
        } else {
            return array(["response" => "Fallo al agregar comentario"]);
        }
        
    }
}
