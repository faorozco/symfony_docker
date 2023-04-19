<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Entity\Proceso;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Undocumented class
 */
class UserSpecialService
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
    public function Crear(Request $request)
    {
        $data = json_decode($request->getContent());
        $usuario = new Usuario();
        $usuario->setLogin($data->{"login"});
        $usuario->setNumeroDocumento($data->{"numero_documento"});
        $usuario->setApellido1($data->{"apellido1"});
        $usuario->setApellido2($data->{"apellido2"});
        $usuario->setNombre1($data->{"nombre1"});
        $usuario->setNombre2($data->{"nombre2"});
        $usuario->setCelular($data->{"celular"});
        $usuario->setEmail($data->{"email"});
        $usuario->setTelefonoFijoResidencia($data->{"telefono_fijo_residencia"});
        $usuario->setDireccionResidencia($data->{"direccion_residencia"});
        $usuario->setGenero($data->{"genero"});
        $usuario->setFechaNacimiento(new \DateTime($data->{"fecha_nacimiento"}));
        $usuario->setTokenValidAfter(new \DateTime());
        $usuario->setClave($this->encoder->encodePassword($usuario, $data->{"clave"}));
        $usuario->setEstadoId($data->{"estado_id"});
        $this->em->persist($usuario);
        $this->em->flush();
        if (isset($data->{"proceso_id"})) {
            $proceso = $this->em->getRepository(Proceso::class)->findOneById($data->{"proceso_id"});            
            if (null !== $proceso) {
                $usuario->setProceso($proceso);                
                $usuario->setSede($proceso->getSede());
            }

        }
        if (isset($data->{"cargo_id"})) {
            $cargo = $this->em->getRepository(Cargo::class)->findOneById($data->{"cargo_id"});
            if (null !== $cargo) {
                $usuario->setCargo($cargo);
            }

        }
        $this->em->persist($usuario);
        $this->em->flush();
        $response = [];
        $response['nombre1']=$usuario->getNombre1();
        $response['apellido1']=$usuario->getApellido1();
        return array("response" => $response);
    }
}
