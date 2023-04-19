<?php

namespace App\Controller;

use App\Entity\Rol;
use App\Entity\Grupo;
use App\Entity\Proceso;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Undocumented class
 */
class GroupCreateService
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
    public function create(Request $request):Grupo
    {
        $grupo = json_decode($request->getContent());
        $newGroup = new Grupo();
        $newGroup->setNombre($grupo->nombre);
        $newGroup->setEstadoId($grupo->estadoId);
        $this->em->persist($newGroup);
        $this->em->flush();
        foreach ($grupo->usuarios as &$usuarioId) {
            $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);
            $newGroup->addUsuario($usuario);
        }
        $this->em->persist($newGroup);
        return $newGroup;
    }
}
