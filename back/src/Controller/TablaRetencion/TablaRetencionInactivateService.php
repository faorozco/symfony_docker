<?php

namespace App\Controller\TablaRetencion;

use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use App\Entity\Formulario;
use App\Entity\Valordocumental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Undocumented class
 */
class TablaRetencionInactivateService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $entityManager;
        $this->encoder = $encoder;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function save(Request $request)
    {
        $tablaRetencion = $this->em->getRepository(TablaRetencion::class)->findOneById($request->attributes->get("id"));
        $tablaRetencion->setEstadoId(0);
        $this->em->persist($tablaRetencion);
        $this->em->flush();
        return $tablaRetencion;
    }
}
