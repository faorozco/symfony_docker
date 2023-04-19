<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\Formulario;
use App\Entity\TablaRetencion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class InactivateNodeService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function inactivate(Request $request)
    {
        //veriricar si el nodo enviado es un tipo documental
        $node_id = $request->attributes->get("id");
        $inactivate = false;
        $hijosActivos = -1;
        //Carga el objeto Estructura Documental relacionado al id entregado
        $node = $this->em->getRepository(EstructuraDocumental::class)
            ->findOneById($node_id);
        //Casos a validar si se puede inactivar un nodo

        //1. Si es un nodo
        // Para este caso se verifica si el nodo tiene hijos activos
        // Si no tiene hijos se procede a inactivar el nodo y se inactiva el regisro en TRD si llega a tener relación
        if (($node->getCodigoDirectorio() !== "0" && $node->getType() != "tipo_documental")) {
            $hijosActivos = $this->em->getRepository(EstructuraDocumental::class)
                ->checkActiveChildNodes($node->getCodigoDirectorio());
        }

        //2. Si es un tipo documental
        // Para este caso vamos a tener presente que en el momento en que se inactiva un nodo se verifica si este tiene relación con un formulario. Si es asi, el formulario se inactiva y también la relación en TRD.
        // 1.1 Validar si el id recibido es de un nodo tipo_documental
        if (($node->getCodigoDirectorio() === "0" && $node->getType() == "tipo_documental")) {
            //Verificar si tiene relación en formulario para inactivar el formulario
            $formulario = $this->em->getRepository(Formulario::class)
                ->findOneBy(array("estructura_documental_id" => $node->getId()));
            if (isset($formulario)) {
                $formulario->setEstadoId(0);
                $this->em->persist($formulario);
            }
            //Verificar si tiene relación en tabla_retencion para inactivar esa relacion
            $trd = $this->em->getRepository(TablaRetencion::class)
                ->findOneBy(array("estructura_documental_id" => $node->getId()));
            if (isset($trd)) {
                $trd->setEstadoId(0);
                $this->em->persist($trd);
            }
            //Inactivar Estructura Documental
            $node->setEstadoId(0);
            $this->em->persist($node);
            $this->em->flush();
            $inactivate = true;
        }
        //Se verifica si algún nodo padre esta activo sin hijos activos
        if ($hijosActivos == 0 && $node->getCodigoDirectorio() !== "0" && $node->getType() != "tipo_documental") {
            //Verificar si tiene relación en tabla_retencion para inactivar esa relacion
            $trd = $this->em->getRepository(TablaRetencion::class)
                ->findOneBy(array("estructura_documental_id" => $node->getId()));
            if (isset($trd)) {
                $trd->setEstadoId(0);
                $this->em->persist($trd);
            }
            //Inactivar Estructura Documental
            $node->setEstadoId(0);
            $this->em->persist($node);
            $this->em->flush();
            $inactivate = true;
        }

        return (array("result" => array("response" => $inactivate)));
    }
}
