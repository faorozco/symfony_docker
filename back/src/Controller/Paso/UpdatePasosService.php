<?php

namespace App\Controller\Paso;

use App\Entity\Paso;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class UpdatePasosService
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
    public function Update(Request $request)
    {
        $data = json_decode($request->getContent());
        if (
            isset($data->{'id'}) && isset($data->{'descripcion'}) &&
            isset($data->{'numero'}) && isset($data->{'plazo'}) &&
            isset($data->{'prioridad'}) && isset($data->{'time'}) 
        ) {
            $paso = $this->em->getRepository(Paso::class)->findOneById($data->{'id'});
            if(isset($paso)){
                $paso->setDescripcion($data->{'descripcion'});
                $paso->setNumero($data->{'numero'});
                $paso->setPlazo($data->{'plazo'});
                $paso->setPrioridad($data->{'prioridad'});
                $paso->setTime($data->{'time'});
                $this->em->persist($paso);
                $this->em->flush();
                return ([array("update" => "true","descripcion" => $data->{'descripcion'})]);
            }else{
                return ([array("update" => "false")]);
            }
        } else {
            return ([array("update" => "false")]);
        }
    }
}
