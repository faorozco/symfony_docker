<?php

namespace App\Utils;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 */
class UsuarioStandard
{
    private $id;

    private $login;

    private $nombreCompleto;

    private $rols;

    private $grupos;

    private $procesos;

    private $empresa;

    private $urlImagen;

    /**f
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
     * Get the value of login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set the value of login
     *
     * @return  self
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get the value of nombreCompleto
     */
    public function getNombreCompleto()
    {
        return $this->nombreCompleto;
    }

    /**
     * Set the value of nombreCompleto
     *
     * @return  self
     */
    public function setNombreCompleto($nombreCompleto)
    {
        $this->nombreCompleto = $nombreCompleto;

        return $this;
    }

    /**
     * Get the value of Rols
     */
    public function getRols()
    {
        return $this->rols;
    }

    /**
     * Set the value of Rols
     *
     * @return  self
     */
    public function setRols($rolesAsignados)
    {
        $rols = array();
        foreach ($rolesAsignados as $key => $rol) {
            $rols[] = array("id" => $rol->getId(), "nombre" => $rol->getNombre());
        }
        $this->rols = $rols;

        return $this;
    }

    /**
     * Get the value of grupos
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * Set the value of grupos
     *
     * @return  self
     */
    public function setGrupos($grupos)
    {
        $this->grupos = $grupos;

        return $this;
    }

    /**
     * Get the value of empresa
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set the value of empresa
     *
     * @return  self
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get the value of procesos
     */
    public function getProcesos()
    {
        return $this->procesos;
    }

    /**
     * Set the value of procesos
     *
     * @return  self
     */
    public function setProcesos($procesos)
    {
        $proceso[] = array("id" => $procesos->getId(), "nombre" => $procesos->getNombre());
        $this->procesos = $proceso;

        return $this;
    }

    /**
     * Get the value of urlImagen
     */
    public function getUrlImagen()
    {
        return $this->urlImagen;
    }

    /**
     * Set the value of urlImagen
     *
     * @return  self
     */
    public function setUrlImagen($urlImagen)
    {
        $this->urlImagen = $urlImagen;

        return $this;
    }
}
