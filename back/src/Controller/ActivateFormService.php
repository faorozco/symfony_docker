<?php

namespace App\Controller;

use App\Entity\Formulario;
use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class ActivateFormService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function activate(Request $request)
    {
        //Capturar el formulario a Inactivar
        $form_id = $request->attributes->get("id");
        $activate = false;
        //Carga el objeto Formulario relacionado al id entregado
        $formulario = $this->em->getRepository(Formulario::class)
            ->findOneById($form_id);

        
        //3. Actualizar estado de formulario a Inactivo=0
        $formulario->setEstadoId(1);
        $this->em->persist($formulario);
        $this->em->flush();
        $activate = true;

        return (array("result" => array("response" => $activate)));
    }
}
