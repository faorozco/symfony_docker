<?php

namespace App\Dto;

class TablaRetencionDto
{
    protected $id;

    protected $version;

    protected $tiempoRetencionArchivoGestion;

    protected $unidadRetencionArchivoGestion;

    protected $tiempoRetencionArchivoCentral;

    protected $unidadRetencionArchivoCentral;

    protected $tipoSoporte;

    protected $disposicionFinalBorrar;

    protected $disposicionFinalConservacionTotal;

    protected $disposicionFinalConservacionDigital;

    protected $disposicionFinalMicrofilmado;

    protected $disposicionFinalSeleccion;

    protected $disposicionFinalMigrar;

    protected $disposicionFinalDigitalizacionMicrofilmacion;

    protected $procedimientoDisposicion;

    protected $leyNormatividad;

    protected $modulo;

    protected $inicioVigencia;

    protected $finVigencia;

    protected $estructuraDocumentalId;

    protected $estadoId;

    protected $tipoDocumentalId;

    protected $descripcion;

    protected $codigoArchivoDocumental;

    protected $valorDocumental;

    protected $tieneFormulario;

    protected $formularioId;

    protected $transferenciaMedioElectronico;

    protected $direccionDocumentosAlmacenadosElectronicamente;

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
     * Get the value of version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of version
     *
     * @return  self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the value of tiempoRetencionArchivoGestion
     */
    public function getTiempoRetencionArchivoGestion()
    {
        return $this->tiempoRetencionArchivoGestion;
    }

    /**
     * Set the value of tiempoRetencionArchivoGestion
     *
     * @return  self
     */
    public function setTiempoRetencionArchivoGestion($tiempoRetencionArchivoGestion = null)
    {
        $this->tiempoRetencionArchivoGestion = $tiempoRetencionArchivoGestion;

        return $this;
    }

    /**
     * Get the value of unidadRetencionArchivoGestion
     */
    public function getUnidadRetencionArchivoGestion()
    {
        return $this->unidadRetencionArchivoGestion;
    }

    /**
     * Set the value of unidadRetencionArchivoGestion
     *
     * @return  self
     */
    public function setUnidadRetencionArchivoGestion($unidadRetencionArchivoGestion)
    {
        $this->unidadRetencionArchivoGestion = $unidadRetencionArchivoGestion;

        return $this;
    }

    /**
     * Get the value of tiempoRetencionArchivoCentral
     */
    public function getTiempoRetencionArchivoCentral()
    {
        return $this->tiempoRetencionArchivoCentral;
    }

    /**
     * Set the value of tiempoRetencionArchivoCentral
     *
     * @return  self
     */
    public function setTiempoRetencionArchivoCentral($tiempoRetencionArchivoCentral)
    {
        $this->tiempoRetencionArchivoCentral = $tiempoRetencionArchivoCentral;

        return $this;
    }

    /**
     * Get the value of unidadRetencionArchivoCentral
     */
    public function getUnidadRetencionArchivoCentral()
    {
        return $this->unidadRetencionArchivoCentral;
    }

    /**
     * Set the value of unidadRetencionArchivoCentral
     *
     * @return  self
     */
    public function setUnidadRetencionArchivoCentral($unidadRetencionArchivoCentral)
    {
        $this->unidadRetencionArchivoCentral = $unidadRetencionArchivoCentral;

        return $this;
    }

    /**
     * Get the value of tipoSoporte
     */
    public function getTipoSoporte()
    {
        return $this->tipoSoporte;
    }

    /**
     * Set the value of tipoSoporte
     *
     * @return  self
     */
    public function setTipoSoporte($tipoSoporte)
    {
        $this->tipoSoporte = $tipoSoporte;

        return $this;
    }

    /**
     * Get the value of disposicionFinalBorrar
     */
    public function getDisposicionFinalBorrar()
    {
        return $this->disposicionFinalBorrar;
    }

    /**
     * Set the value of disposicionFinalBorrar
     *
     * @return  self
     */
    public function setDisposicionFinalBorrar($disposicionFinalBorrar)
    {
        $this->disposicionFinalBorrar = $disposicionFinalBorrar;

        return $this;
    }

    /**
     * Get the value of disposicionFinalConservacionTotal
     */
    public function getDisposicionFinalConservacionTotal()
    {
        return $this->disposicionFinalConservacionTotal;
    }

    /**
     * Set the value of disposicionFinalConservacionTotal
     *
     * @return  self
     */
    public function setDisposicionFinalConservacionTotal($disposicionFinalConservacionTotal)
    {
        $this->disposicionFinalConservacionTotal = $disposicionFinalConservacionTotal;

        return $this;
    }

    /**
     * Get the value of disposicionFinalConservacionDigital
     */
    public function getDisposicionFinalConservacionDigital()
    {
        return $this->disposicionFinalConservacionDigital;
    }

    /**
     * Set the value of disposicionFinalConservacionDigital
     *
     * @return  self
     */
    public function setDisposicionFinalConservacionDigital($disposicionFinalConservacionDigital)
    {
        $this->disposicionFinalConservacionDigital = $disposicionFinalConservacionDigital;

        return $this;
    }

    /**
     * Get the value of disposicionFinalMicrofilmado
     */
    public function getDisposicionFinalMicrofilmado()
    {
        return $this->disposicionFinalMicrofilmado;
    }

    /**
     * Set the value of disposicionFinalMicrofilmado
     *
     * @return  self
     */
    public function setDisposicionFinalMicrofilmado($disposicionFinalMicrofilmado)
    {
        $this->disposicionFinalMicrofilmado = $disposicionFinalMicrofilmado;

        return $this;
    }

    /**
     * Get the value of disposicionFinalSeleccion
     */
    public function getDisposicionFinalSeleccion()
    {
        return $this->disposicionFinalSeleccion;
    }

    /**
     * Set the value of disposicionFinalSeleccion
     *
     * @return  self
     */
    public function setDisposicionFinalSeleccion($disposicionFinalSeleccion)
    {
        $this->disposicionFinalSeleccion = $disposicionFinalSeleccion;

        return $this;
    }

    /**
     * Get the value of disposicion_final_migrar
     *
     * @return boolean
     */
    public function getDisposicionFinalMigrar()
    {
        return $this->disposicion_final_migrar;
    }

    /**
     * Set the value of disposicion_final_migrar
     *
     * @return self
     */
    public function setDisposicionFinalMigrar($disposicion_final_migrar)
    {
        $this->disposicion_final_migrar = $disposicion_final_migrar;

        return $this;
    }

    /**
     * Get the value of disposicion_final_digitalizacion_microfilmacion
     *
     * @return boolean
     */
    public function getDisposicionFinalDigitalizacionMicrofilmacion()
    {
        return $this->disposicion_final_digitalizacion_microfilmacion;
    }

    /**
     * Set the value of disposicion_final_digitalizacion_microfilmacion
     *
     * @return \App\Entity\TablaRetencion
     */
    public function setDisposicionFinalDigitalizacionMicrofilmacion($disposicion_final_digitalizacion_microfilmacion)
    {
        $this->disposicion_final_digitalizacion_microfilmacion = $disposicion_final_digitalizacion_microfilmacion;

        return $this;
    }

    /**
     * Get the value of procedimientoDisposicion
     */
    public function getProcedimientoDisposicion()
    {
        return $this->procedimientoDisposicion;
    }

    /**
     * Set the value of procedimientoDisposicion
     *
     * @return  self
     */
    public function setProcedimientoDisposicion($procedimientoDisposicion)
    {
        $this->procedimientoDisposicion = $procedimientoDisposicion;

        return $this;
    }

    /**
     * Get the value of modulo
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set the value of modulo
     *
     * @return  self
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;

        return $this;
    }

    /**
     * Get the value of inicioVigencia
     */
    public function getInicioVigencia()
    {
        return $this->inicioVigencia;
    }

    /**
     * Set the value of inicioVigencia
     *
     * @return  self
     */
    public function setInicioVigencia($inicioVigencia)
    {
        $this->inicioVigencia = $inicioVigencia;

        return $this;
    }

    /**
     * Get the value of finVigencia
     */
    public function getFinVigencia()
    {
        return $this->finVigencia;
    }

    /**
     * Set the value of finVigencia
     *
     * @return  self
     */
    public function setFinVigencia($finVigencia)
    {
        $this->finVigencia = $finVigencia;

        return $this;
    }

    /**
     * Get the value of estructuraDocumentalId
     */
    public function getEstructuraDocumentalId()
    {
        return $this->estructuraDocumentalId;
    }

    /**
     * Set the value of estructuraDocumentalId
     *
     * @return  self
     */
    public function setEstructuraDocumentalId($estructuraDocumentalId)
    {
        $this->estructuraDocumentalId = $estructuraDocumentalId;

        return $this;
    }

    /**
     * Get the value of estadoId
     */
    public function getEstadoId()
    {
        return $this->estadoId;
    }

    /**
     * Set the value of estadoId
     *
     * @return  self
     */
    public function setEstadoId($estadoId)
    {
        $this->estadoId = $estadoId;

        return $this;
    }

    /**
     * Get the value of tipoDocumentalId
     */
    public function getTipoDocumentalId()
    {
        return $this->tipoDocumentalId;
    }

    /**
     * Set the value of tipoDocumentalId
     *
     * @return  self
     */
    public function setTipoDocumentalId($tipoDocumentalId)
    {
        $this->tipoDocumentalId = $tipoDocumentalId;

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
     * Get the value of codigoArchivoDocumental
     */
    public function getCodigoArchivoDocumental()
    {
        return $this->codigoArchivoDocumental;
    }

    /**
     * Set the value of codigoArchivoDocumental
     *
     * @return  self
     */
    public function setCodigoArchivoDocumental($codigoArchivoDocumental)
    {
        $this->codigoArchivoDocumental = $codigoArchivoDocumental;

        return $this;
    }

    /**
     * Get the value of valorDocumental
     */
    public function getValorDocumental()
    {
        return $this->valorDocumental;
    }

    /**
     * Set the value of valorDocumental
     *
     * @return  self
     */
    public function setValorDocumental($valorDocumental)
    {
        $this->valorDocumental = $valorDocumental;

        return $this;
    }

    /**
     * Get the value of tieneFormulario
     */
    public function getTieneFormulario()
    {
        return $this->tieneFormulario;
    }

    /**
     * Set the value of tieneFormulario
     *
     * @return  self
     */
    public function setTieneFormulario($tieneFormulario)
    {
        $this->tieneFormulario = $tieneFormulario;

        return $this;
    }

    /**
     * Get the value of formularioId
     */
    public function getFormularioId()
    {
        return $this->formularioId;
    }

    /**
     * Set the value of formularioId
     *
     * @return  self
     */
    public function setFormularioId($formularioId)
    {
        $this->formularioId = $formularioId;

        return $this;
    }

    /**
     * Get the value of leyNormatividad
     */
    public function getLeyNormatividad()
    {
        return $this->leyNormatividad;
    }

    /**
     * Set the value of leyNormatividad
     *
     * @return  self
     */
    public function setLeyNormatividad($leyNormatividad)
    {
        $this->leyNormatividad = $leyNormatividad;

        return $this;
    }

    /**
     * Get the value of transferenciaMedioElectronico
     */
    public function getTransferenciaMedioElectronico()
    {
        return $this->transferenciaMedioElectronico;
    }

    /**
     * Set the value of transferenciaMedioElectronico
     *
     * @return  self
     */
    public function setTransferenciaMedioElectronico($transferenciaMedioElectronico)
    {
        $this->transferenciaMedioElectronico = $transferenciaMedioElectronico;

        return $this;
    }

    /**
     * Get the value of direccionDocumentosAlmacenadosElectronicamente
     */
    public function getDireccionDocumentosAlmacenadosElectronicamente()
    {
        return $this->direccionDocumentosAlmacenadosElectronicamente;
    }

    /**
     * Set the value of direccionDocumentosAlmacenadosElectronicamente
     *
     * @return  self
     */
    public function setDireccionDocumentosAlmacenadosElectronicamente($direccionDocumentosAlmacenadosElectronicamente)
    {
        $this->direccionDocumentosAlmacenadosElectronicamente = $direccionDocumentosAlmacenadosElectronicamente;

        return $this;
    }
}
