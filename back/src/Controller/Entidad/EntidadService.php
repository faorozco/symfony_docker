<?php

namespace App\Controller\Entidad;

use App\Entity\Entidad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class EntidadService
{
    private $_em;

    private $filterColumns = ["estado_id", "id", "clave", "token_valid_after", "try", "active_sesion"];

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
     * function para cargar los nombres de las columnas de las tablas en entidades
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function entidadColumnas($entidadId, $filter)
    {
        $entidad = $this->em->getRepository(Entidad::class)->findOneById($entidadId);
        $columnas = $this->em->getClassMetadata('App\Entity\\' . $entidad->getNombre())->getColumnNames();

        $columnasShow = [];
        foreach($columnas as $columna) {
            if (!in_array($columna, $this->filterColumns)) {

                if($filter == "") {
                    $columnasShow[] = $columna;
                } else if(strpos($columna, $filter) !== false) {
                    $columnasShow[] = $columna;
                }
                
            }
        }

        if (isset($columnasShow)) {
            sort($columnasShow);
            return $columnasShow;
        } else {
            return array("response" => "Error consultando las columnas de la entidad");
        }
    }
}
