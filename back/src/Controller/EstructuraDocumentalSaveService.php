<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Undocumented class
 */
class EstructuraDocumentalSaveService
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

    public function save($node)
    {
        $fondo = $_ENV["FONDO"];
        $seccion = $_ENV["SECCION"];
        $subseccion = $_ENV["SECCION"] + $_ENV["SUBSECCION"];
        $serie = $_ENV["SECCION"] + $_ENV["SUBSECCION"] + $_ENV["SERIE"];
        $subserie = $_ENV["SECCION"] + $_ENV["SUBSECCION"] + $_ENV["SERIE"] + $_ENV["SUBSERIE"];

        //1.Los tipos documentales deben ser creados solo por series o subseries.
        if ($node->{"codigoDirectorio"} == "" && (strlen($node->{"codigoDirectorioPadre"}) == $fondo || strlen($node->{"codigoDirectorioPadre"}) == $seccion || strlen($node->{"codigoDirectorioPadre"}) == $subseccion)) {
            return array("result" => array("response" => "Los tipos documentales solo pueden ser creados por series o subseries"));
        }
        //2.Las secciones y subsecciones solo deben ser creadas desde el fondo.
        else if ((strlen($node->{"codigoDirectorio"}) == $seccion || strlen($node->{"codigoDirectorio"}) == $subseccion) && (strlen($node->{"codigoDirectorioPadre"}) == $serie || strlen($node->{"codigoDirectorioPadre"}) == $subserie)) {
            return array("result" => array("response" => "Las secciones y subsecciones solo deben ser creadas desde el fondo"));
        } else {
            $newNode = new EstructuraDocumental();
            $newNode->setCodigoDirectorio($node->{"codigoDirectorio"});
            $newNode->setDescripcion($node->{"descripcion"});
            $newNode->setCodigoDirectorioPadre($node->{"codigoDirectorioPadre"});
            $newNode->setIdestructura($node->{"idEstructura"});
            if(isset($node->{"type"})){
                $newNode->setType($node->{"type"});
            }
            if(isset($node->{"peso"})){
                $newNode->setPeso($node->{"peso"});
            }
            $newNode->setEstadoId($node->{"estadoId"});
            $this->em->persist($newNode);
            $this->em->flush();
            return array("result" => array("response" => $newNode));
        }
    }
}
