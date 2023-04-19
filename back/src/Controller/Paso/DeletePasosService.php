<?php

namespace App\Controller\Paso;

use App\Entity\Paso;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class DeletePasosService
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
    public function Delete(Request $request)
    {
        $data = json_decode($request->getContent());
        if (isset($data->{'paso_id'})) {
        $paso = $this->em->getRepository(Paso::class)->findOneById($data->{'paso_id'});
        if(isset($paso)){

            $paso->setEstadoId(0);
            $this->em->persist($paso);
            $this->em->flush();

            return ([array("delete" => "true")]);
        }else{
            return ([array("delete" => "false")]);
        }
        } else {
            return ([array("delete" => "false")]);
        }
    }
}
