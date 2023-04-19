<?php

namespace App\Controller;

use App\Entity\ConsultaMaestra;
use App\Entity\Formulario;
use App\Entity\FormularioVersion;
use App\Entity\Grupo;
use App\Entity\Rol;
use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ConsultaMaestraSaveService
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
        $data = json_decode($request->getContent());
        $consultaMaestra = new ConsultaMaestra();
        $consultaMaestra->setNombre($data->{'nombre'});
        $consultaMaestra->setDetalle($data->{'detalle'});
        $consultaMaestra->setEstadoId($data->{'estadoId'});

        $grupos = $data->{'grupos'};
        foreach($grupos as $grupoId) {
            $grupo = $this->em->getRepository(Grupo::class)->findOneById(str_replace("/api/grupos/", "", $grupoId));
            $consultaMaestra->addGrupo($grupo);
        }
        
        $roles = $data->{'roles'};
        foreach($roles as $rolId) {
            $rol = $this->em->getRepository(Rol::class)->findOneById(str_replace("/api/rol/", "", $rolId));
            $consultaMaestra->addRol($rol);
        }

        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($data->{'formularioVersionId'});
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($formularioVersion->getFormularioId());
        $consultaMaestra->setFormulario($formulario);
        
        //$consultaMaestra = $this->em->getRepository(ConsultaMaestra::class)->findOneById($request->attributes->get("id"));

        $this->em->persist($consultaMaestra);
        $this->em->flush();
        if (isset($consultaMaestra)) {
            return $consultaMaestra;
        } else {
            return array("response" => "Fallo al guardar la consulta maestra");
        }
        
    }
}
