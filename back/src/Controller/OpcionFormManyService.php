<?php

namespace App\Controller;

use App\Entity\Formulario;
use App\Entity\FormularioVersion;
use App\Entity\OpcionFormulario;
use App\Entity\OpcionFormularioVersion;
use App\Entity\Permite;
use App\Entity\Grupo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Collections\ArrayCollection;

class OpcionFormManyService
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

    public function get($request)
    {
        $data = json_decode($request->getContent());

        $opcionesFormulario = $data->{"permites"};
        $formularioId = $data->{"formularioId"};


        $formularioRepository = $this->em->getRepository(Formulario::class);
        $permiteRepository = $this->em->getRepository(Permite::class);

        $opcionFormularios = new ArrayCollection();

        $formulario = $formularioRepository->findOneById($formularioId);

        foreach ($opcionesFormulario as $opcion) {

            $opcionFormulario = new OpcionFormulario();

            $opcionFormulario->setFormulario($formulario);
            $opcionFormulario->setPermite($permiteRepository->findOneById($opcion->{"permite_id"}));
            $opcionFormulario->setConfiguraciones($opcion->{"configuraciones"});

            if (isset($opcion->{"acciones"})) {
                $opcionFormulario->setAcciones($opcion->{"acciones"});
            }

            $opcionFormularios[] = $opcionFormulario;
            $this->em->persist($opcionFormulario);
        }

        $this->em->flush();

        foreach ($opcionFormularios as $opcion) {
            $formulariosVersion = $this->em->getRepository(FormularioVersion::class)->findBy(array('formulario_id' => $opcion->getFormulario()->getId()));

            if (!empty($formulariosVersion)) {
                foreach ($formulariosVersion as $formularioVersion) {
                    $opcionVersion = new OpcionFormularioVersion();
                    $opcionVersion->setFormularioVersion($formularioVersion);
                    $opcionVersion->setOpcionFormulario($opcion);

                    $this->em->persist($opcionVersion);
                }
            }
        }

        $formulario->clearGrupo();

        $grupos = $data->{'grupos'};
        foreach($grupos as $grupo) {
            $grupo = $this->em->getRepository(Grupo::class)->findOneById($grupo->id);
            $formulario->addGrupo($grupo);
        }

        $this->em->persist($formulario);
        $this->em->flush();
    }
}
