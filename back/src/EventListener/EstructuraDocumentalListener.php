<?php

namespace App\EventListener;

//Se deben agregar todas al entidades que van a tener registro de auditoria
use App\Entity\EstructuraDocumental;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EstructuraDocumentalListener
{

    private $tokenStorage;
    private $usuario;
    private $em;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $entityManager;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

        if (null !== $this->tokenStorage->getToken()) {
            $entityManager = $args->getEntityManager();
            $uow = $entityManager->getUnitOfWork();
            $entity = $args->getEntity();
            if ($entity instanceof EstructuraDocumental) {
                $this->usuario = $this->tokenStorage->getToken()->getUser();
                $cambios = $uow->getEntityChangeSet($entity);
                if (isset($cambios["codigo_directorio"])) {
                    $codigoDirectorioPadreAnterior = $cambios["codigo_directorio"][0];
                    $codigoDirectorioPadreNuevo = $cambios["codigo_directorio"][1];
                    //Creo una query update que actualice el codigo_directorio_padre nuevo por el anterior
                    return $this->em->getRepository(EstructuraDocumental::class)->updateParentDirectory($codigoDirectorioPadreAnterior, $codigoDirectorioPadreNuevo);
                }
            }
        }
    }
}
