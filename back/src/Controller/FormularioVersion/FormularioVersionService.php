<?php

namespace App\Controller\FormularioVersion;

use App\Entity\FlujoTrabajoVersion;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use App\Entity\Usuario;
use App\Entity\EstructuraDocumental;
use App\Entity\Grupo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class FormularioVersionService
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
    public function getPorFlujoTrabajoVersion(Request $request)
    {
        $flujoTrabajoVersionId = $request->attributes->get('id');
        $flujoTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->findOneById(array("id" => $flujoTrabajoVersionId));
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById(array("id" => $flujoTrabajoVersion->getFormularioVersionId()));

        if (isset($formularioVersion)) {
            return $formularioVersion;
        } else {
            return array("response" => "No hay formulario asociado al flujo");
        }
    }

    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function getPorRegistro(Request $request)
    {
        $registroId = $request->attributes->get('id');
        $registro = $this->em->getRepository(Registro::class)->findOneById($registroId);
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById(array("id" => $registro->getFormularioVersionId()));

        if (isset($formularioVersion)) {
            return $formularioVersion;
        } else {
            return array("response" => "No hay formulario asociado al flujo");
        }
    }
}
