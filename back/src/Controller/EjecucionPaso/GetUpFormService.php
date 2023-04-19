<?php

namespace App\Controller\EjecucionPaso;

use App\Entity\Archivo;
use App\Entity\Registro;
use App\Entity\RegistroCampo;
use App\Controller\RelacionadosByRegistroService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Registro\RegistroService;
use App\Entity\CampoFormularioVersion;

/**
 * Undocumented class
 */
class GetUpFormService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager,RelacionadosByRegistroService $relacionadosByRegistroService, RegistroService $registroService)
    {
        $this->em = $entityManager;
        $this->reFormServices = $relacionadosByRegistroService;
        $this->registroService = $registroService;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function get(Request $request)
    {
        $data = json_decode($request->getContent());

        if(
        isset($data->{'registroId'})
        ){
            
            $registroCampo = $this->em->getRepository(RegistroCampo::class)->findOneBy(array("registro_id" => $data->{'registroId'}));
            if(is_null($registroCampo)){
                $registrOrigenId = $data->{'registroId'};
            }else{
                $registrOrigenId = $registroCampo->getRegistroIdOrigen();
            }
            $relacionados = $this->reFormServices->Get($registrOrigenId); 
            $relacionadosFinal = [];
            foreach ($relacionados as $relacionado){
                $registroId = $relacionado['registroId']; 
                $registro = $this->em->getRepository(Registro::class)->findOneById($registroId);
                $formularioVersionId = $registro->getFormularioVersionId();
                $camposIndices = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array("formulario_version_id" => $formularioVersionId , "indice" => 1));
                $relacionado['campoValue'] = $registro->getRadicado();
                foreach ($camposIndices as $campoIndice){
                    $relacionado['campoValue'] =$relacionado['campoValue']  ."  " . $this->registroService->valueByFieldVersionAndRegister($campoIndice->getId(),$registro)['valor'];
                }

                $relacionado['radicado'] = $registro->getRadicado();
                $relacionadosFinal[] = $relacionado;
            };  

            $padreValores = $this->padreResponse($registrOrigenId);
            $response = array('response' => array ( 'hijos'=>$relacionadosFinal, 'padre'=> $padreValores));

            return $response;
        } else {
            return array(["response" => "Fallo al consultar relacionados"]);
        }


    }

    private function padreResponse($registroId){
        $registro = $this->em->getRepository(Registro::class)->findOneById($registroId);
        $campos = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array('formulario_version_id'=>$registro->getFormularioVersionId(),'indice'=>1));
        $indices = ''.$registro->getRadicado();
        foreach ( $campos as $campo){
            $indices = $indices .' '.$this->registroService->valueByFieldVersionAndRegister($campo->getId(),$registro)['valor'];
        }

        $padre = [];
        $padre["radicado"] = $registro->getRadicado();
        $padre["indices"] = $indices;
        $padre["campoValue"] = $indices;
        $padre["registroId"] = $registroId;
        return $padre;

    }
}
