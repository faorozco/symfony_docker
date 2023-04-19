<?php

namespace App\Controller\Usuario;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Hydra;


class LogoutService 
{

        public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
        {
            $this->em = $entityManager;
            $this->tokenStorage = $tokenStorage;
        }

        public function logout(): array
        {   $userAutenticate = $this->tokenStorage->getToken()->getUser();
            $hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $_ENV['LICENSED']]);
            $user = $this->em->getRepository(Usuario::class)->findOneById($userAutenticate->getId());
            $sesion = $hydra->getActual();
            $sesion--;
            $user->setActiveSesion(false);
            $user->setTokenValidAfter(new \DateTime());
            $hydra->setActual($sesion);
            $this->em->persist($hydra);
            $this->em->persist($user);
            $this->em->flush();
            return ([
                'logout' => true
            ]);
        }
}