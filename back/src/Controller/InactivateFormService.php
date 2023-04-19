<?php

namespace App\Controller;

use App\Entity\Formulario;
use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class InactivateFormService
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
        //Capturar el formulario a Inactivar
        $form_id = $request->attributes->get("id");
        $inactivate = false;
        //Carga el objeto Formulario relacionado al id entregado
        $formulario = $this->em->getRepository(Formulario::class)
            ->findOneById($form_id);
        //Casos a validar si se puede inactivar un nodo
        //1. Al inactivar un formulario liberar relación con estructura documental
        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)
            ->findOneBy(array("formulario"=>$form_id));

        if ($estructuraDocumental != null) {
            //Limpiar relación estructura_documental_id en formulario
            $estructuraDocumental->setFormulario(null);
            $this->em->persist($estructuraDocumental);

            //2. Verificar si hay relación en TRD para inactivarla y limpiar la relación en formulario.
            $tablaRetencion = $this->em->getRepository(TablaRetencion::class)->findOneBy(array("estructura_documental_id" => $estructuraDocumental->getId()));
            if ((null !== $tablaRetencion)) {
                //Inactivar Tabla Retención
                $tablaRetencion->setEstadoId(0);
                $this->em->persist($tablaRetencion);
            }
        }

        
        //3. Actualizar estado de formulario a Inactivo=0
        $formulario->setEstadoId(0);
        $this->em->persist($formulario);
        $this->em->flush();
        $inactivate = true;

        return (array("result" => array("response" => $inactivate)));
    }
}
