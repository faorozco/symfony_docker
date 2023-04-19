<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Formulario;
use App\Entity\EstructuraDocumental;
use App\Entity\CampoFormulario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class FormAssociatedWithDocumentalEstructureService
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
    public function get(Request $request)
    {
        $estructuraDocumentalId = $request->attributes->get("estructuraDocumentalId");

        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findOneById($estructuraDocumentalId);
        
        $formulario = null;
        if($estructuraDocumental->getType() == 'tipo_documental' && $estructuraDocumental->getFormulario() != null) {
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($estructuraDocumental->getFormulario()->getId());
            $estructuraDocumental->setFormulario($formulario);
        }
        
        $formularios = new ArrayCollection();
        if($formulario != null) {
            $formularios[] = $formulario;
        }
        return $formularios;

    }
}
