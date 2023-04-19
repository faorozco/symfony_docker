<?php
namespace App\DataFixtures;

use App\Entity\Usuario;
use App\Entity\Cargo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuariosFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 1; $i++) {
            $usuario = new Usuario();
            $cargo=$manager->getRepository(Cargo::class)->findOneBy(array("id" => 1));
            // $encoder = $this->container->get('security.encoder_factory')->getEncoder($usuario);
            $usuario->setLogin('cperez');
            $usuario->setProcesoId(1);
            $usuario->setNumeroDocumento("10011184");
            $usuario->setApellido1("Pérez" . $i);
            $usuario->setApellido2("Rivera" . $i);
            $usuario->setNombre1("Carlos" . $i);
            $usuario->setNombre2("Alfonso" . $i);
            $usuario->setCelular("321757606");
            $usuario->setEmail("ingcarlosperez@gmail.com");
            $usuario->setTelefonoFijoResidencia("3207308");
            $usuario->setDireccionResidencia("Cra 10. No.63-28");
            $usuario->setGenero("M");
            $usuario->setFechaNacimiento(new \DateTime("1979-06-05"));
            $usuario->setClave($this->encoder->encodePassword($usuario, 1234));
            $usuario->setEstadoId(1);
            $usuario->setCargo($cargo);
            $manager->persist($usuario);
        }

        $manager->flush();
    }
}
