<?php

namespace App\Controller\Tercero;

use App\Entity\Tercero;
use App\Entity\Ciudad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class TerceroService
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
     * Create Tercero function
     *
     * @param string $request
     *
     * @return Tercero
     */
    public function create($tercero)
    {
        $newTercero = new Tercero();
        $newTercero->setIdentificacion($tercero->{"identificacion"});
        $newTercero->setNombre($tercero->{"nombre"});
        $newTercero->setDireccion($tercero->{"direccion"});
        $newTercero->setTelefono($tercero->{"telefono"});
        $newTercero->setCelular($tercero->{"celular"});
        $newTercero->setCorreoElectronico($tercero->{"correoElectronico"});
        $newTercero->setEstadoId($tercero->{"estadoId"});

        $this->em->persist($newTercero);
        $this->em->flush();

        $ciudad = $this->em->getRepository(Ciudad::class)->findOneById($tercero->{"ciudad"});
        $newTercero->setCiudad($ciudad);
        $this->em->persist($newTercero);
        $this->em->flush();

        return $newTercero;    
    }

    /**
     * Create Tercero function
     *
     * @param string $request
     *
     * @return Tercero
     */
    public function createTercero($tercero)
    {
        $newTercero = new Tercero();
        $newTercero->setIdentificacion($tercero["identificacion"]);
        $newTercero->setNombre($tercero["nombre"]);
        $newTercero->setDireccion($tercero["direccion"]);
        $newTercero->setTelefono($tercero["telefono"]);
        $newTercero->setCelular($tercero["celular"]);
        $newTercero->setCorreoElectronico($tercero["correo_electronico"]);
        $newTercero->setEstadoId($tercero["estado_id"]);

        $this->em->persist($newTercero);
        $this->em->flush();

        $ciudad = $this->em->getRepository(Ciudad::class)->findOneById($tercero["ciudad"]);
        $newTercero->setCiudad($ciudad);
        $this->em->persist($newTercero);
        $this->em->flush();

        return $newTercero;    
    }
}
