<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\FormularioVersion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FormPermiteService
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

    public function get($request)
    {

        $id = $request->attributes->get("id");
        $registroId = $request->attributes->get("registro_id");
        if (null !== $registroId) {
            $registro_id = $request->attributes->get("registro_id");
        }
        $usuario = $this->tokenStorage->getToken()->getUser();

        $gruposUsuario = $usuario->getGrupos();

        $gruposSeguridad = array();

        foreach ($gruposUsuario as $grupo) {
            $gruposSeguridad[] = $grupo->getId();
        }

        $queryBuilder = $this->em->createQueryBuilder();
        
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($id);
        
        $entityResults = $queryBuilder
        ->select("permite.id, permite.nombre, permite.descripcion, opciones.configuraciones, opciones.acciones, permite.orden")
        ->from('App\\Entity\\Permite', 'permite')
        ->join('permite.opcionFormularios', 'opciones', Expr\Join::WITH, 'opciones.formulario_id = :form_id')
        ->setParameter('form_id', $formularioVersion->getFormularioId())
        ->orderBy('permite.orden', 'ASC')
        ->getQuery()
        ->execute();

        $result = array();    
            foreach ($entityResults as $data) {

                if (isset($data['configuraciones']['grupos'])) {
                    foreach ($data['configuraciones']['grupos'] as $grupo) {
                        if (in_array($grupo['id'], $gruposSeguridad)) {

                            $acciones = array();

                            if ($data['acciones']) {

                                foreach ($data['acciones'] as $accion => $gruposAccion) {

                                    if (!empty($gruposAccion)) {

                                        $cantidadGrupos = 0;

                                        foreach ($gruposAccion as $value) {
                                            if (in_array($value['id'], $gruposSeguridad)) {
                                                $cantidadGrupos++;
                                            }
                                        }

                                        if ($cantidadGrupos > 0) {
                                            $acciones[] = array("nombre" => $accion, "permite" => true);
                                        } else {
                                            $acciones[] = array("nombre" => $accion, "permite" => false);
                                        }

                                    } else {
                                        $acciones[] = array("nombre" => $accion, "permite" => false);
                                    }

                                }

                            }

                            $result[$data['id']] = array('id' => $data['id'], 'nombre' => $data['nombre'], 'acciones' => $acciones, 'orden' => $data['orden']);
                        }
                    }
                }

            }
         if (count($result) == 0 && null !== $registroId) {
            //Verificar si el registro tiene archivo tipo documental cargado
            $archivo = $this->em->getRepository(Archivo::class)->FindOneBy(array("registro_id" => $registroId, "tipo_documental" => true));
            if (isset($archivo)) {
                $acciones=array();
                $acciones[]=array("nombre"=>"bajar_archivo", "permite"=>true);
                $acciones[]=array("nombre"=>"subir_archivo", "permite"=>false);
                $result[0] = array('id' => 2, 'nombre' => 'Archivo', 'acciones' => $acciones);
            }
        }
        //Si es asi habilitar la pestaña permite Archivo

        return $result;

    }
}
