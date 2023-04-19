<?php

namespace App\Controller;

use App\Entity\CampoFormularioVersion;
use App\Entity\Registro;
use App\Entity\RegistroCampo;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class RelacionadosByRegistroService
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
    public function Get($registroId,$queryString, $page, $itemsPerPage, $estado, $orderBy)
    {
        //1. Traer los registros que estan relacionados con este registro.
        // Lo primero es verificar que campos de este registro son de tipo campo
        $registro = $this->em->getRepository(Registro::class)->findOneById($registroId);
        //Ahora verificamos si algun campo de este formulario fue usado como tipo formulario
        $camposFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findAllVersionByFormularioVersion($this->em, $registro->getFormularioVersionId());
        $result = array();
        $hashExist = array();
        $entityResults = array();
        $registrosRelacionados = $this->em->getRepository(RegistroCampo::class)->findRelationedChild($registro->getId(),$queryString, $page, $itemsPerPage, $estado, $orderBy);
        foreach ($registrosRelacionados as $registroRelacionado) {
            $result1 = array();
            $result1["id"] = $registroRelacionado->getId();
            $result1["FormularioId"] = $registroRelacionado->getRegistro()->getFormularioVersion()->getId();
            $result1["campoFormularioVersionId"] = $registroRelacionado->getCampoFormularioVersionId();
            $result1["fechaHoraRegistro"] = $registroRelacionado->getRegistro()->getFechaHora();
            $result1["nombreFormulario"] = $registroRelacionado->getRegistro()->getFormularioVersion()->getNombre();
            $result1["registroId"] = $registroRelacionado->getRegistro()->getId();
            $result1["remitente"] = $registroRelacionado->getRegistro()->getUsuario()->getNombre1() . " " . $registroRelacionado->getRegistro()->getUsuario()->getNombre2() . " " . $registroRelacionado->getRegistro()->getUsuario()->getApellido1() . " " . $registroRelacionado->getRegistro()->getUsuario()->getApellido2();

            if(!isset($hashExist[$result1["registroId"]])) {
                $entityResults[] = $result1;
                $hashExist[$result1["registroId"]] = true;
            }
        }


        if (count($entityResults) > 0) {
            $result = array_merge($entityResults);
        }

        $registroCampos = $this->em->getRepository(RegistroCampo::class)->findByRegistro($registro);
        foreach ($registroCampos as $registroCampo) {
            $registroOrigenId = $registroCampo->getRegistroIdOrigen();
            //Consulto el tipo de campo con el que se construyó el tipo de campo Formulario Relacionado
            $campoFormularioVersionBase = $this->em->getRepository(CampoFormularioVersion::class)->findOneById($registroCampo->getCampoFormularioVersion()->getCampoFormularioVersionId());
            $camposFormularioVersionRegistro = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array("campo_formulario_id" => $campoFormularioVersionBase->getCampoFormularioId()));

            $ids = [];
            foreach ($camposFormularioVersionRegistro as $camposId) {
                $ids[] = $camposId;
            }
            //Extraigo el tipo de Campo de $campoFormularioVersionBase
            $tipoCampo = $campoFormularioVersionBase->getTipoCampo();
            // $tipoCampo se cruza con $registroOrigenId
            $queryBuilder = $this->em->createQueryBuilder();
            $entityResults = $queryBuilder
                ->select("e.id as id, e.registro_id as registroId, registro.fecha_hora as fechaHoraRegistro, e.campo_formulario_version_id as campoFormularioVersionId, formulario.id as FormularioId, formulario.nombre as nombreFormulario, CONCAT(usuario.nombre1,' ', usuario.nombre2,' ',usuario.apellido1,' ', usuario.apellido2) as remitente")
                ->from('App\\Entity\\Registro' . $tipoCampo, 'e')
                ->leftJoin('e.registro', 'registro')
                ->leftJoin('registro.usuario', 'usuario')
                ->leftJoin('registro.formularioVersion', 'formulario')
                ->where("e.registro_id = :registro_id")
                ->andWhere("e.campo_formulario_version_id IN (:ids)")
                ->setParameter('registro_id', $registroOrigenId)
                ->setParameter('ids', $ids)
                ->getQuery()
                ->execute();

            if (count($entityResults) > 0) {
                foreach ($entityResults as $entityResult) {
                    if ($registro->getId() != $registroOrigenId && (count($hashExist) == 0 || !isset($hashExist[$entityResult["registroId"]]) || !$hashExist[$entityResult["registroId"]])) {
                        $result[] = $entityResult;
                    }
                }
            }
        }
        // Teniendo el tipo campo, se consulta en las entidades registro con que
        if (isset($result)) {
            return $result;
        } else {
            return [];
        }
    }
}
