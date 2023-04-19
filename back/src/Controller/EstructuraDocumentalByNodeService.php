<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use App\Utils\EstructuraDocumentalStandard;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Undocumented class
 */
class EstructuraDocumentalByNodeService
{
    private $_em;
    private $_estructuraDocumentalStandard;
    private $_childNodes;

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
     * @param string $login
     *
     * @return EstructuraDocumentalStandard
     */
    public function generarEstructuraDocumental(string $node, $mostrarPeso): EstructuraDocumentalStandard
    {
        $this->estructuraDocumentalStandard = new EstructuraDocumentalStandard();
        if ($node == "") {
            $node = $_ENV["CODIGO_DIRECTORIO_FONDO"];
        }
        $this->node = $node;
        //realizar una consulta para la información principal del nodo
        //si nodo es vacio buscamos todos los nodos hijos de nodo vacio
        //sirve para cargar los primeros elementos del arbol.
        $estructuraDocumentalRoot = $this->em->getRepository(EstructuraDocumental::class)
            ->findOneBy(array("codigo_directorio" => $this->node, "estado_id" => "1"));
        //realizar una consulta para saber cuales son los hijos de ese nodo
        $estructuraDocumentalChildren = $this->em->getRepository(EstructuraDocumental::class)
            ->findBy(
                array("codigo_directorio_padre" => $this->node, "estado_id" => "1"),
                array('codigo_directorio_padre' => 'ASC', 'codigo_directorio' => 'ASC', 'peso' => 'ASC')
            );
        if (isset($estructuraDocumentalRoot)) {
            //Guardar la información en estructuraDocumentalStandard
            $this->estructuraDocumentalStandard->setId($estructuraDocumentalRoot->getId());
            $this->estructuraDocumentalStandard->setCodigoDirectorioPadre($estructuraDocumentalRoot->getCodigoDirectorioPadre());
            if ($estructuraDocumentalRoot->getCodigoDirectorio() == $_ENV["CODIGO_DIRECTORIO_FONDO"]) {
                $this->estructuraDocumentalStandard->setCodigoDirectorio("");
            } else {
                $this->estructuraDocumentalStandard->setCodigoDirectorio($estructuraDocumentalRoot->getCodigoDirectorio());
            }
            $this->estructuraDocumentalStandard->setDescripcion($estructuraDocumentalRoot->getDescripcion());
            $this->estructuraDocumentalStandard->setIdEstructura($estructuraDocumentalRoot->getIdEstructura());
            if ($node == "") {
                $this->estructuraDocumentalStandard->setMenuNuevo(true);
                $this->estructuraDocumentalStandard->setTieneHijos(true);
                $this->estructuraDocumentalStandard->setGeneraTrd(true);
            }
            //Calculo de digitos totales por cada nivel del arbol
            $fondo = $_ENV["FONDO"];
            $seccion = $_ENV["SECCION"];
            $subseccion = $_ENV["SECCION"] + $_ENV["SUBSECCION"];
            $serie = $_ENV["SECCION"] + $_ENV["SUBSECCION"] + $_ENV["SERIE"];
            $subserie = $_ENV["SECCION"] + $_ENV["SUBSECCION"] + $_ENV["SERIE"] + $_ENV["SUBSERIE"];
            //Cantidad de caraceteres para el nodo padre
            $cantidad_caracteres = strlen($estructuraDocumentalRoot->getCodigoDirectorio());
            //Si es un tipo documental se verifica que su código de directorio sea 0
            if ($estructuraDocumentalRoot->getCodigoDirectorio() === 0) {
                $this->estructuraDocumentalStandard->setIcon($_ENV["TIPO_DOCUMENTAL_ICON"]);
            } else if ($cantidad_caracteres == 3 && $estructuraDocumentalRoot->getCodigoDirectorioPadre() == "") {
                $this->estructuraDocumentalStandard->setIcon($_ENV["SECCION_ICON"]);
            } else {
                if ($estructuraDocumentalRoot->getCodigoDirectorio() == $_ENV["CODIGO_DIRECTORIO_FONDO"]) {
                    $this->estructuraDocumentalStandard->setIcon($_ENV["FONDO_ICON"]);
                } else {
                    switch ($cantidad_caracteres) {
                        case $subseccion:
                            $this->estructuraDocumentalStandard->setIcon($_ENV["SUBSECCION_ICON"]);
                            break;
                        case $serie:
                            $this->estructuraDocumentalStandard->setIcon($_ENV["SERIE_ICON"]);
                            break;
                        case $subserie:
                            $this->estructuraDocumentalStandard->setIcon($_ENV["SUBSERIE_ICON"]);
                            break;
                        default:
                            $this->estructuraDocumentalStandard->setIcon($_ENV["DEFAULT_ICON"]);
                            break;
                    }
                }
            }

            $this->childNodes = array();

            foreach ($estructuraDocumentalChildren as $childNode) {
                $hasTrd = null;
                $trdId = null;
                $generaTrd = false;
                $haveChildrens = false;
                $inactivate = false;
                //verificar si es nodo hoja o subcarpeta
                $childrensNodes = $this->em->getRepository(EstructuraDocumental::class)
                    ->findBy(array("codigo_directorio_padre" => $childNode->getCodigoDirectorio()));
                if (count($childrensNodes) > 0) {
                    $haveChildrens = true;
                } else if (count($childrensNodes) == 0) {
                    $haveChildrens = false;
                }

                //Verifica si un nodo tiene hijos activos para colocar inactivar false
                $hijosActivos = 0;
                if (($childNode->getCodigoDirectorio() !== "0" && $childNode->getType() != "tipo_documental")) {
                    $hijosActivos = $this->em->getRepository(EstructuraDocumental::class)
                        ->checkActiveChildNodes($childNode->getCodigoDirectorio());
                }

                //verifica si un nodo es tipo documental o no tiene hijos activos para agregar opción inactivar
                if (($childNode->getCodigoDirectorio() === "0" && $childNode->getType() == "tipo_documental") || $hijosActivos == 0) {
                    $inactivate = true;
                }

                if ($childNode->getCodigoDirectorio() === "0" || (strlen($childNode->getCodigoDirectorio()) >= $serie && strlen($childNode->getCodigoDirectorio()) <= $subserie)) {
                    $trd = $this->em->getRepository(TablaRetencion::class)
                        ->findOneBy(array("estructura_documental_id" => $childNode->getId()));
                    if (isset($trd)) {
                        $hasTrd = true;
                        $trdId = $trd->getId();
                    } else if (!isset($trd)) {
                        $hasTrd = false;
                    }
                }
                if (strlen($childNode->getCodigoDirectorio()) >= $seccion && strlen($childNode->getCodigoDirectorio()) <= $subseccion && $childNode->getCodigoDirectorio() !== "0") {
                    $trd = $this->em->getRepository(TablaRetencion::class)
                        ->findOneBy(array("estructura_documental_id" => $childNode->getId()));
                    $generaTrd = true;
                }

                if ($childNode->getCodigoDirectorioPadre() === "0") {
                    $parentDirectory = null;
                } else {
                    $parentDirectory = $childNode->getCodigoDirectorioPadre();
                }
                if ($childNode->getCodigoDirectorio() === "0") {
                    $directory = null;
                    $estadoSinNuevo = false;
                } else {
                    $directory = $childNode->getCodigoDirectorio();
                    $estadoSinNuevo = true;
                }
                $cantidad_caracteres = strlen($childNode->getCodigoDirectorio());
                //Si es un tipo documental se verifica que su código de directorio sea 0
                if ($childNode->getCodigoDirectorio() === "0") {
                    $icon = $_ENV["TIPO_DOCUMENTAL_ICON"];
                } else if ($cantidad_caracteres == 3 && $childNode->getCodigoDirectorioPadre() == "") {
                    $icon = $_ENV["SECCION_ICON"];
                } else {
                    //Cantidad de caraceteres para los nodos hijo
                    switch ($cantidad_caracteres) {
                        case $fondo:
                            $icon = $_ENV["FONDO_ICON"];
                            break;
                        case $seccion:
                            $icon = $_ENV["SECCION_ICON"];
                            break;
                        case $subseccion:
                            $icon = $_ENV["SUBSECCION_ICON"];
                            break;
                        case $serie:
                            $icon = $_ENV["SERIE_ICON"];
                            break;
                        case $subserie:
                            $icon = $_ENV["SUBSERIE_ICON"];
                            break;
                        default:
                            $icon = $_ENV["DEFAULT_ICON"];
                            break;
                    }
                }
                $descripcion = $childNode->getDescripcion();
                if (strpos($descripcion, '|') !== false) {
                    $detalleDescripcion = explode("|", $descripcion);
                    if (null !== $mostrarPeso && $detalleDescripcion[1]>0) {
                        $descripcionFormateada = $detalleDescripcion[0] . "  (" . $detalleDescripcion[1] . ")";
                    }
                    else{
                        $descripcionFormateada = $detalleDescripcion[0];
                    }
                }else{
                    $descripcionFormateada=$descripcion;
                }

                $this->childNodes[] = array(
                    "id" => $childNode->getId(),
                    "codigoDirectorioPadre" => $parentDirectory,
                    "codigoDirectorio" => $directory,
                    "descripcion" => $descripcionFormateada,
                    "idEstructura" => $childNode->getIdEstructura(),
                    "tieneHijos" => $haveChildrens,
                    "tieneTrd" => $hasTrd,
                    "inactivar" => $inactivate,
                    "generaTrd" => $generaTrd,
                    "trdId" => $trdId,
                    "type" => $childNode->getType(),
                    "menuNuevo" => $estadoSinNuevo,
                    "icon" => $icon,
                );
            }
            $this->estructuraDocumentalStandard->setChildren($this->childNodes);
            return $this->estructuraDocumentalStandard;
        }
        return $this->estructuraDocumentalStandard;
    }
}
