<?php

namespace App\Controller;

use App\Entity\Rol;
use App\Entity\Grupo;
use App\Entity\Proceso;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Undocumented class
 */
class GroupUpdateSpecialService
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
    public function Update(Request $request):Grupo
    {
        $data = json_decode($request->getContent());
        $grupo = $this->em->getRepository(Grupo::class)->findOneById($request->attributes->get("id"));

        if (isset($data->{"nombre"})) {
            $grupo->setNombre($data->{"nombre"});
        }

        if (isset($data->{"estadoId"})) {
            $grupo->setEstadoId($data->{"estadoId"});
        }
        
        if (isset($data->{"usuarios"})) {
            //se borran los objetos de la relación actual            
                $rawQuery = 'DELETE FROM usuario_grupo where grupo_id = ?';
        
                $stmt = $this->em->getConnection()->prepare($rawQuery);
                $stmt->bindValue(1, $request->attributes->get("id"));
                $stmt->execute();
            //se agregan los nuevos objetos
            foreach ($data->{"usuarios"} as $usuarioId) {
                //$usuarioArray=explode("/api/usuarios/",$usuario);
                $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);
                $grupo->addUsuario($usuario);
            }
        }
        $this->em->persist($grupo);
        return $grupo;
    }
}
