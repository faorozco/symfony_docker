<?php

namespace App\Controller;

use App\Entity\Plantilla;
use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class TemplateUpdateService
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
     * @return Group
     */
    public function update(Request $request):Plantilla
    {
        $plantilla = json_decode($request->getContent());
        $newTemplate = $this->em->getRepository(Plantilla::class)->findOneById($plantilla->id);
        $newTemplate->setContenido($plantilla->contenido);
        $newTemplate->setDescripcion($plantilla->descripcion);
        $newTemplate->setEstadoId($plantilla->estadoId);
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($plantilla->formulario);
        $newTemplate->setFormulario($formulario);

        $this->em->persist($newTemplate);
        return $newTemplate;
    }
}
