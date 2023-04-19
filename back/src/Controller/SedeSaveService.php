<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use App\Entity\Valordocumental;
use App\Entity\Sede;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class SedeSaveService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * Crear o actualizar una sede function
     *
     * @param string $request
     *
     * @return Sede
     */
    public function save(Request $request)
    {
        $data = json_decode($request->getContent());
        $id = $data->{"id"};

        $sede = null;
        $empresa = null;
        if ($id == null) {
            $usuario = $this->tokenStorage->getToken()->getUser();
            $sedeId = $usuario->getSedeId();
            $sedeUser = $this->em->getRepository(Sede::class)->findOneById($sedeId);
            $empresa = $sedeUser->getEmpresa();

            $sede = new Sede();
            $sede->setEmpresa($empresa);
            $sede->setEstadoId(1);
        } else {
            $sede = $this->em->getRepository(Sede::class)->findOneById($id);
        }

        $sede->setCodigoInterno($data->{"codigoInterno"});
        $sede->setNombre($data->{"nombre"});
        $sede->setDireccion($data->{"direccion"});
        $sede->setPbx($data->{"pbx"});
        $sede->setCelular($data->{"celular"});
        $sede->setEmail($data->{"email"});
        $sede->setUrl($data->{"url"});

        $this->em->persist($sede);
        $this->em->flush();
        return $sede;
    }
}
