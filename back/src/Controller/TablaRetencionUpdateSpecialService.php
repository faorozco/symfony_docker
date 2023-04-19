<?php

namespace App\Controller;

use App\Entity\TablaRetencion;
use App\Entity\Valordocumental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Undocumented class
 */
class TablaRetencionUpdateSpecialService
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
    public function Actualizar(Request $request)
    {
        $data = json_decode($request->getContent());
        $tablaRetencion = $this->em->getRepository(TablaRetencion::class)->findOneById($request->attributes->get("id"));

        if (isset($data->{"valordocumentals"})) {
            //se borran los objetos de la relación actual
            foreach ($tablaRetencion->getValorDocumentals() as $valorDocumental) {
                $tablaRetencion->removeValorDocumental($valorDocumental);
            }

            //se agregan los nuevos objetos
            foreach ($data->{"valordocumentals"} as $valorDocumentalId) {

                $valorDocumental = $this->em->getRepository(Valordocumental::class)->findOneById($valorDocumentalId);
                if(null !== $valorDocumental) {
                    $tablaRetencion->addValorDocumental($valorDocumental);
                } else {
                    throw new \Exception('Valor Documental no existe.');

                }
            }

        }

        $this->em->persist($tablaRetencion);
        return $tablaRetencion;
    }
}
