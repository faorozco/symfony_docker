<?php

namespace App\Controller;

use App\Entity\Rol;
use App\Entity\Grupo;
use App\Entity\Proceso;
use App\Entity\Usuario;
use App\Entity\Sede;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Hydra;

/**
 * Undocumented class
 */
class UserUpdateSpecialService
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
        $usuario = $this->em->getRepository(Usuario::class)->findOneById($request->attributes->get("id"));
        if (isset($data->{"login"})) {
            $usuario->setLogin($data->{"login"});
        }
        
        if (isset($data->{"proceso_id"})) {
            $usuario->setProcesoId($data->{"proceso_id"});
        }
        
        if (isset($data->{"sedeId"})) {
            $usuario->setSedeId($data->{"sedeId"});
        }

        if (isset($data->{"numero_documento"})) {
            $usuario->setNumeroDocumento($data->{"numero_documento"});
        }

        if (isset($data->{"apellido1"})) {
            $usuario->setApellido1($data->{"apellido1"});
        }

        if (isset($data->{"apellido2"})) {
            $usuario->setApellido2($data->{"apellido2"});
        }
        if (isset($data->{"nombre1"})) {
            $usuario->setNombre1($data->{"nombre1"});
        }

        if (isset($data->{"nombre2"})) {
            $usuario->setNombre2($data->{"nombre2"});
        }
        if (isset($data->{"celular"})) {
            $usuario->setCelular($data->{"celular"});
        }

        if (isset($data->{"email"})) {
            $usuario->setEmail($data->{"email"});
        }

        if (isset($data->{"telefono_fijo_residencia"})) {
            $usuario->setTelefonoFijoResidencia($data->{"telefono_fijo_residencia"});
        }

        if (isset($data->{"direccion_residencia"})) {
            $usuario->setDireccionResidencia($data->{"direccion_residencia"});
        }

        if (isset($data->{"genero"})) {
            $usuario->setGenero($data->{"genero"});
        }

        if (isset($data->{"fecha_nacimiento"})) {
            $usuario->setFechaNacimiento(new \DateTime($data->{"fecha_nacimiento"}));
        }
        if (isset($data->{"clave"}) && $data->{"clave"} != "") {
            $usuario->setClave($this->encoder->encodePassword($usuario, $data->{"clave"}));
        }

        if (isset($data->{"estado_id"})) {
            $usuario->setEstadoId($data->{"estado_id"});
        }
        if (isset($data->{"rols"})) {
            //se borran los objetos de la relación actual
            foreach ($usuario->getRols() as $rol) {
                $usuario->removeRol($rol);
            }
            //se agregan los nuevos objetos
            foreach ($data->{"rols"} as $rolId) {
                $rol = $this->em->getRepository(Rol::class)->findOneById($rolId);
                $usuario->addRol($rol);
            }
        }
        if (isset($data->{"grupos"})) {
            //se borran los objetos de la relación actual
            foreach ($usuario->getGrupos() as $grupoUsuario) {                
                $usuario->removeGrupo($grupoUsuario);
            }
            //se agregan los nuevos objetos
            foreach ($data->{"grupos"} as $grupoId) {
                $grupo = $this->em->getRepository(Grupo::class)->findOneById($grupoId);
                $usuario->addGrupo($grupo);
            }
        }

        if (isset($data->{"cargo_id"})) {
            $usuario->setCargoId($data->{"cargo_id"});
        }
        if (isset($data->{"activeSesion"})) {
            if($usuario->getActiveSesion() && !$data->{"activeSesion"}){
                $hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $_ENV['LICENSED']]);
                $sesion = $hydra->getActual();
                $sesion = $sesion <= 0 ? 0: $sesion - 1;
                $usuario->setActiveSesion(0);
                $usuario->setTokenValidAfter(new \DateTime());
                $hydra->setActual($sesion);
                $this->em->persist($hydra);
            }
        }
        if (isset($data->{"bloqueo"})) {
            $usuario->setBloqueo($data->{"bloqueo"});
        }
        $usuario->setTokenValidAfter(new \DateTime());
        $this->em->persist($usuario);
        $this->em->flush();
        $response = [];
        $response['nombre1']=$usuario->getNombre1();
        $response['apellido1']=$usuario->getApellido1();
        return array("response" => $response);
    }
}
