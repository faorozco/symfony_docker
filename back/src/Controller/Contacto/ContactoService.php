<?php

namespace App\Controller\Contacto;

use App\Entity\Contacto;
use App\Entity\Ciudad;
use App\Entity\Tercero;
use App\Entity\TipoContacto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ContactoService
{
    private $_em;
    private $_result;

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
     * Create Contacto function
     *
     * @param string $request
     *
     * @return Contacto
     */
    public function create($Contacto)
    {
        $newContacto = new Contacto();
        $newContacto->setCargo($Contacto->{"cargo"});
        $newContacto->setCelular($Contacto->{"celular"});
        $newContacto->setComentario($Contacto->{"comentario"});
        $newContacto->setCorreo($Contacto->{"correo"});
        $newContacto->setEstadoId($Contacto->{"estadoId"});
        $newContacto->setNombre($Contacto->{"nombre"});
        $newContacto->setTelefonoFijo($Contacto->{"telefonoFijo"});
        $newContacto->setTratamiento($Contacto->{"tratamiento"});

        $this->em->persist($newContacto);
        $this->em->flush();

        $ciudad = $this->em->getRepository(Ciudad::class)->findOneById($Contacto->{"ciudadId"});
        $newContacto->setCiudad($ciudad);

        $tercero = $this->em->getRepository(Tercero::class)->findOneById($Contacto->{"terceroId"});
        $newContacto->setTercero($tercero);


        $contacto = $this->em->getRepository(TipoContacto::class)->findOneById($Contacto->{"tipoContactoId"});
        $newContacto->setTipoContacto($contacto);


        $this->em->persist($newContacto);
        $this->em->flush();

        return $newContacto;    
    }
}
