<?php

namespace App\Controller;

use App\Entity\Plantilla;
use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Undocumented class
 */
class TemplateCreateService
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
    public function create(Request $request):Plantilla
    {
        $plantilla = json_decode($request->getContent());
        $newTemplate = new Plantilla();
        $newTemplate->setContenido($plantilla->contenido);
        $newTemplate->setDescripcion($plantilla->descripcion);
        $newTemplate->setEstadoId($plantilla->estadoId);
        $this->em->persist($newTemplate);
        $this->em->flush();
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($plantilla->formulario);
        $newTemplate->setFormulario($formulario);

        $this->em->persist($newTemplate);
        return $newTemplate;
    }
}
