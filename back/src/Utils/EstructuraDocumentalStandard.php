<?php

namespace App\Utils;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 */
class EstructuraDocumentalStandard
{

    private $id;

    private $codigoDirectorioPadre;

    private $codigoDirectorio;

    private $descripcion;

    private $idEstructura;

    private $children;

    private $tiene_hijos;

    private $menu_nuevo;

    private $genera_trd;

    private $icon;

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of codigoDirectorioPadre
     */
    public function getCodigoDirectorioPadre()
    {
        return $this->codigoDirectorioPadre;
    }

    /**
     * Set the value of codigoDirectorioPadre
     *
     * @return  self
     */
    public function setCodigoDirectorioPadre($codigoDirectorioPadre)
    {
        $this->codigoDirectorioPadre = $codigoDirectorioPadre;

        return $this;
    }

    /**
     * Get the value of codigoDirectorio
     */
    public function getCodigoDirectorio()
    {
        return $this->codigoDirectorio;
    }

    /**
     * Set the value of codigoDirectorio
     *
     * @return  self
     */
    public function setCodigoDirectorio($codigoDirectorio)
    {
        $this->codigoDirectorio = $codigoDirectorio;

        return $this;
    }

    /**
     * Get the value of descripcion
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     *
     * @return  self
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of idEstructura
     */
    public function getIdEstructura()
    {
        return $this->idEstructura;
    }

    /**
     * Set the value of idEstructura
     *
     * @return  self
     */
    public function setIdEstructura($idEstructura)
    {
        $this->idEstructura = $idEstructura;

        return $this;
    }

    /**
     * Get the value of children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set the value of children
     *
     * @return  self
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get the value of tiene_hijos
     */
    public function getTieneHijos()
    {
        return $this->tiene_hijos;
    }

    /**
     * Set the value of tiene_hijos
     *
     * @return  self
     */
    public function setTieneHijos($tiene_hijos)
    {
        $this->tiene_hijos = $tiene_hijos;

        return $this;
    }

    /**
     * Get the value of menu_nuevo
     */
    public function getMenuNuevo()
    {
        return $this->menu_nuevo;
    }

    /**
     * Set the value of menu_nuevo
     *
     * @return  self
     */
    public function setMenuNuevo($menu_nuevo)
    {
        $this->menu_nuevo = $menu_nuevo;

        return $this;
    }

    /**
     * Get the value of icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set the value of icon
     *
     * @return  self
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the value of genera_trd
     */
    public function getGeneraTrd()
    {
        return $this->genera_trd;
    }

    /**
     * Set the value of genera_trd
     *
     * @return  self
     */
    public function setGeneraTrd($genera_trd)
    {
        $this->genera_trd = $genera_trd;

        return $this;
    }
}
