<?php

namespace App\Controller\EjecucionPaso;

use App\Entity\Archivo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use App\Entity\Registro;
use App\Entity\FormularioVersion;
use App\Entity\CampoFormularioVersion;

/**
 * Undocumented class
 */
class GetFormFlujoService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

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
        isset($data->{'idPaso'})
        ){
            $registro = $this->em->getRepository(Registro::class)->findOneById($data->{'idPaso'});
            $form = $this->em->getRepository(FormularioVersion::class)->findOneById($registro->getFormularioVersionId());
            $campos = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array('formulario_version_id'=>$form->getId(),'tipo_campo'=>'Formulario'));
            $camposArray = (array) $campos;
            return $campos ;
        } else {
            return array(["response" => "Fallo al enviar comentarios"]);
        }


    }
}
