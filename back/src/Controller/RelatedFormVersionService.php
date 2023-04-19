<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CampoFormularioVersion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RelatedFormVersionService
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
        $query = $request->query->get('query');
        $campoFormularioVersionRepository = $this->em->getRepository(CampoFormularioVersion::class);
        //Consulto los formularios que tienen campo_formulario_id de los campos encontrados
        $formulariorelacionados = $campoFormularioVersionRepository->findFormulariosRelacionados($this->em, $request->attributes->get('id'), $query);
        return ($formulariorelacionados);

    }
}
