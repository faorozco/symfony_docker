<?php

namespace App\Controller;

use App\Entity\Formulario;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class DuplicateFormService
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
    public function Duplicate(Request $request)
    {
        //consulto el formulario que se quiere duplicar
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($request->attributes->get("id"));
        //luego de eso invoco el metodo mágico clone de PHP el cual se sobreescribió en la entidad formulario
        $formularioClonado = clone $formulario;
        //Se le cambia el nombre al nuevo formulario para identificarlo facilmente.
        $formularioClonado->setNombre($formulario->getNombre() . " (Copia)");
        $formularioClonado->setEstructuraDocumental();
        $formularioClonado->setVersion(null);
        $this->em->persist($formularioClonado);
        $this->em->flush();
        if (isset($formularioClonado)) {
            return $formularioClonado;
        } else {
            return array("response" => "Formulario no se pudo clonar");
        }
    }
}
