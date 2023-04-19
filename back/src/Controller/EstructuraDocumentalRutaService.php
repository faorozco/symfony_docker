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
class EstructuraDocumentalRutaService
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
        $estructuraDocumentalId = $request->attributes->get("id");

        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findOneByIdWithRoute($this->em, $estructuraDocumentalId) ;
        
        return $estructuraDocumental;

    }
}
