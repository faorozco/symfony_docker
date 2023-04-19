<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Lista;

/**
 * Undocumented class
 */
class ListaSaveService
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
     * @return Usuario
     */
    public function save(Request $request)
    {
        $id = $request->attributes->get('id');
        $data = json_decode($request->getContent());
        $lista = $this->em->getRepository(Lista::class)->findOneById($id);

        $lista->setNombre($data->{'nombre'});
        $lista->setEstadoId($data->{'estadoId'});

        $this->em->persist($lista);
        $this->em->flush();
        return $lista;
    }
}
