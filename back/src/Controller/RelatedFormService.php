<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Formulario;
use App\Entity\CampoFormulario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class RelatedFormService
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
        $formularioRepository = $this->em->getRepository(Formulario::class);
        $campoFormularioRepository = $this->em->getRepository(CampoFormulario::class);
        $camposFormulario = $campoFormularioRepository->findBy(array("formulario_id" => $request->attributes->get('id'), "estado_id" => 1));
        $camposFormularioIds=array();
        foreach ($camposFormulario as $campoFormulario) {
            $camposFormularioIds[]=$campoFormulario->getId();
        }
        //Consulto los formularios que tienen campo_formulario_id de los campos encontrados
        $formulariorelacionados = $campoFormularioRepository->findFormulariosRelacionados($camposFormularioIds, $query);
        return ($formulariorelacionados);

    }
}
