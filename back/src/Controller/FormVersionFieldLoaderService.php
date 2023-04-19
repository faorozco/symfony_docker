<?php

namespace App\Controller;

use App\Entity\CampoFormularioVersion;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class FormVersionFieldLoaderService
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
    public function get($request)
    {
        $ejecucionPasoId = $request->query->get('ejecucionPasoId');
        $formularioVersionId = $request->attributes->get('id');
        if($ejecucionPasoId != null) {
            $resultado = $this->em->getRepository(CampoFormularioVersion::class)
            ->FormFieldLoaderByFlow($this->em, $formularioVersionId, $ejecucionPasoId);
        } else {
            $resultado = $this->em->getRepository(CampoFormularioVersion::class)
            ->FormFieldLoader($formularioVersionId);
        }

        return $resultado;
    }
}
