<?php

namespace App\Repository;

use App\Entity\Formulario;
use App\Entity\TablaRetencion;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class FormularioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formulario::class);
    }

    public function getByUser($query, $usuario, $em): array
    {
        $formulariosRelacionados = array();
        //traigo los grupo relacionados con el usuario autenticado
        $grupos = $usuario->getGrupos();
        foreach ($grupos as $key => $grupo) {
            $formularios = $grupo->getFormularios();
            $currentDate = new DateTime();
            $listForm = $formularios->getValues();
            for($i = 0; $i < sizeof($listForm); $i++) {
                $formulario = $listForm[$i];
                $estructuraDocumentalId = $formulario->getEstructuraDocumentalId();
                if (null !== $estructuraDocumentalId) {
                    $trd = $em->getRepository(TablaRetencion::class)->findOneBy(array("estructura_documental_id" => $estructuraDocumentalId));
                    if (isset($trd)) {
                        if ($formulario->getTipoFormulario() == 4 && $formulario->getEstadoId() == 1) {
                            if (null === $formulario->getFinVigencia()) {
                                $finVigencia = $currentDate;
                            } else {
                                $finVigencia = $formulario->getFinVigencia();
                            }
                            if ($currentDate >= $formulario->getInicioVigencia() && $currentDate <= $finVigencia) {
                                $formName = $formulario->getNombre();
                                if ($query != "") {
                                    if (strpos(strtolower($formulario->getNombre()), strtolower($query)) !== false) {
                                        $formulariosRelacionados[] = array("id" => $formulario->getId(), "nombre" => $formName);
                                    }
                                } else if ($query == "") {
                                    $formulariosRelacionados[] = array("id" => $formulario->getId(), "nombre" => $formName);
                                }
                            }
                        }
                    }
                }
            }
        }

        return (array_map("unserialize", array_unique(array_map("serialize", $formulariosRelacionados))));
    }

    public function getFormulariosRelacionados($formularioId)
    {
        //Primero debo consultar con que formularios esta relacionado este formulario.
        //Esto se hace a través de sus campos
        //Consulto los campos del formulario

    }

    public function getFormulariosSinEstructuraDocumental($em, $query) {
        $sql = $this->createQueryBuilder('f')
        ->select('f')
        ->where('f.estado_id = 1')
        ->andWhere('f.nombre like :query')
        ->andWhere('f.estructura_documental_id IS NULL')
        ->setParameter('query', "%" . $query . "%");
    
        return $sql->getQuery()->getArrayResult();
    }

    public function getFormulariosSinTRD($em, $query) {
        $sql = $this->createQueryBuilder('f')
        ->select('f')
        ->innerJoin('f.estructuraDocumental', 'ed', Expr\Join::WITH, 'ed.id = f.estructura_documental_id AND ed.tabla_retencion_id IS NULL')
        ->where('f.estado_id = 1')
        ->andWhere('f.nombre like :query')
        ->andWhere('f.estructura_documental_id IS NOT NULL')
        ->setParameter('query', "%" . $query . "%");
    
        return $sql->getQuery()->getArrayResult();
    }

    public function list($filtro,$paginaActual,$mostrarInactivo,$size)
    {
        $query = $this->createQueryBuilder("formulario");

        $estado = 1;
        if($mostrarInactivo=="true"){
            $estado = 0;
        }

        if($filtro == true){
            $query = $query->Where("formulario.estado_id = :estado")
            ->andWhere("formulario.nombre like :filter ")
            ->setParameter("estado",$estado)
            ->setParameter("filter", '%'.$filtro.'%')
            ->setFirstResult(($paginaActual - 1) * $size)
            ->orderBy('formulario.id', 'ASC')
            ->setMaxResults($size)
            ->getQuery();
        }else{
            $query = $query->Where("formulario.estado_id = :estado")
            ->setParameter("estado",$estado)
            ->setFirstResult(($paginaActual - 1) * $size)
            ->orderBy('formulario.id', 'ASC')
            ->setMaxResults($size)
            ->getQuery();
        }


        $doctrinePaginator = new DoctrinePaginator($query);
        //IMPORTANTE: Como usar Paginator con consultas Escalares
        $doctrinePaginator->setUseOutputWalkers(false);

        return new Paginator($doctrinePaginator);


    }
}
