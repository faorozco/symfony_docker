<?php

namespace App\Controller;

use App\Entity\OpcionFormulario;
use App\Entity\OpcionFormularioVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OpcionFormDeleteService
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

    public function get($id)
    {

        $opcionesFormulario = $this->em->getRepository(OpcionFormulario::class)->findBy(array('formulario_id' => $id));

        if (!empty($opcionesFormulario)) {
            foreach ($opcionesFormulario as $opcionForm) {
                $opcionesFormularioVersion = $this->em->getRepository(OpcionFormularioVersion::class)->findBy(array('opcion_formulario_id' => $opcionForm->getId()));

                if (!empty($opcionesFormularioVersion)) {
                    foreach ($opcionesFormularioVersion as $opcionFormVersion) {

                        $this->em->remove($opcionFormVersion);
                    }

                    $this->em->flush();

                }

                $this->em->remove($opcionForm);
            }

            $this->em->flush();

        }

    }
}
