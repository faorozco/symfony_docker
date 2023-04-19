<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CurrentVersion;
use App\Controller\GenerarVersion;
use App\Controller\GetVersions;
use App\Controller\TablaRetencionFindOneByIdEspecial;
use App\Controller\TablaRetencionGetEspecial;
use App\Controller\TablaRetencionUpdateSpecial;
use App\Controller\TablaRetencionImport;
use App\Controller\TablaRetencion\TablaRetencionActivate;
use App\Controller\TablaRetencion\TablaRetencionInactivate;
use App\Controller\TablaRetencionSave;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\RetentionTableCreate;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
 *          "method"="POST",
 *          "path"="/tabla_retencions",
 *          "controller"=RetentionTableCreate::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "getEspecial"={
 *         "method"="GET",
 *         "path"="/tabla_retencions/special",
 *         "controller"=TablaRetencionGetEspecial::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "generateversion"={
 *         "method"="GET",
 *         "path"="/tabla_retencions/generateversion",
 *         "controller"=GenerarVersion::class,
 *      },
 *      "getversions"={
 *         "method"="GET",
 *         "path"="/tabla_retencions/getversions",
 *         "controller"=GetVersions::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "getcurrentversion"={
 *         "method"="GET",
 *         "path"="/tabla_retencions/getcurrentversion",
 *         "controller"=CurrentVersion::class,
 *      },
 *      "import"={
 *         "method"="POST",
 *         "path"="/tabla_retencions/import",
 *         "controller"=TablaRetencionImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *    },
 *   itemOperations={
 *      "save"={
 *         "method"="PUT",
 *         "path"="/tabla_retencions/{id}",
 *         "controller"=TablaRetencionSave::class
*        },
 *      "get"={
 *         "method"="GET",
 *         "path"="/tabla_retencions/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *     "update"={
 *         "method"="PUT",
 *         "path"="/tabla_retencions/{id}/valordocumentals",
 *         "controller"=TablaRetencionUpdateSpecial::class
 *     },
 *      "activate"={
 *         "method"="PUT",
 *         "path"="/tabla_retencions/{id}/activate",
 *         "controller"=TablaRetencionActivate::class
 *     },
 *      "inactivate"={
 *         "method"="PUT",
 *         "path"="/tabla_retencions/{id}/inactivate",
 *         "controller"=TablaRetencionInactivate::class
 *     },
 *      "findOndeByIdEspecial"={
 *         "method"="GET",
 *         "path"="/tabla_retencions/{id}/special",
 *         "controller"=TablaRetencionFindOneByIdEspecial::class
 *      },
 *  }
 * )
 * App\Entity\TablaRetencion
 *
 * @ORM\Entity(repositoryClass="App\Repository\TablaRetencionRepository")
 * @ORM\Table(name="tabla_retencion", indexes={@ORM\Index(name="fk_tabla_retencion_estructura_documental1_idx", columns={"estructura_documental_id"})})
 */
class TablaRetencion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $version;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha_version;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tiempo_retencion_archivo_gestion;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $unidad_retencion_archivo_gestion;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tiempo_retencion_archivo_central;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $unidad_retencion_archivo_central;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $tipo_soporte;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_borrar;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_conservacion_total;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_conservacion_digital;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_microfilmado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_seleccion;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_digitalizacion_microfilmacion;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $disposicion_final_migrar;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    protected $procedimiento_disposicion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $ley_normatividad;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $modulo;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inicio_vigencia;

    // /**
    //  * @ORM\Column(type="date", nullable=true)
    //  */
    // protected $fin_vigencia;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $transferencia_medio_electronico;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $direccion_documentos_almacenados_electronicamente;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */

    protected $estructura_documental_id;

    /**
     * Muchas TRDs tienen muchos Valores Documentales
     * @ORM\ManyToMany(targetEntity="ValorDocumental", inversedBy="tablaRetencionsVersion")
     * @ORM\JoinTable(
     *  name="trd_valordocumental",
     *  joinColumns={
     *      @ORM\JoinColumn(name="tabla_retencion_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="valordocumental_id", referencedColumnName="id")
     *  }
     * )
     */

    protected $valorDocumentals;

    /**
     * @ORM\OneToOne(targetEntity="EstructuraDocumental", inversedBy="tablaRetencion")
     * @ORM\JoinColumn(name="estructura_documental_id", referencedColumnName="id", nullable=false)
     */
    protected $estructuraDocumental;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_change;

    public function __construct()
    {
        $this->valorDocumentals = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\TablaRetencion
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of version.
     *
     * @param integer $version
     * @return \App\Entity\TablaRetencion
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the value of version.
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of tiempo_retencion_archivo_gestion.
     *
     * @param string $tiempo_retencion_archivo_gestion
     * @return \App\Entity\TablaRetencion
     */
    public function setTiempoRetencionArchivoGestion($tiempo_retencion_archivo_gestion)
    {
        $this->tiempo_retencion_archivo_gestion = $tiempo_retencion_archivo_gestion;

        return $this;
    }

    /**
     * Get the value of tiempo_retencion_archivo_gestion.
     *
     * @return string
     */
    public function getTiempoRetencionArchivoGestion()
    {
        return $this->tiempo_retencion_archivo_gestion;
    }

    /**
     * Set the value of unidad_retencion_archivo_gestion.
     *
     * @param string $unidad_retencion_archivo_gestion
     * @return \App\Entity\TablaRetencion
     */
    public function setUnidadRetencionArchivoGestion($unidad_retencion_archivo_gestion)
    {
        $this->unidad_retencion_archivo_gestion = $unidad_retencion_archivo_gestion;

        return $this;
    }

    /**
     * Get the value of unidad_retencion_archivo_gestion.
     *
     * @return string
     */
    public function getUnidadRetencionArchivoGestion()
    {
        return $this->unidad_retencion_archivo_gestion;
    }

    /**
     * Set the value of tiempo_retencion_archivo_central.
     *
     * @param string $tiempo_retencion_archivo_central
     * @return \App\Entity\TablaRetencion
     */
    public function setTiempoRetencionArchivoCentral($tiempo_retencion_archivo_central)
    {
        $this->tiempo_retencion_archivo_central = $tiempo_retencion_archivo_central;

        return $this;
    }

    /**
     * Get the value of tiempo_retencion_archivo_central.
     *
     * @return string
     */
    public function getTiempoRetencionArchivoCentral()
    {
        return $this->tiempo_retencion_archivo_central;
    }

    /**
     * Set the value of unidad_retencion_archivo_central.
     *
     * @param string $unidad_retencion_archivo_central
     * @return \App\Entity\TablaRetencion
     */
    public function setUnidadRetencionArchivoCentral($unidad_retencion_archivo_central)
    {
        $this->unidad_retencion_archivo_central = $unidad_retencion_archivo_central;

        return $this;
    }

    /**
     * Get the value of unidad_retencion_archivo_central.
     *
     * @return string
     */
    public function getUnidadRetencionArchivoCentral()
    {
        return $this->unidad_retencion_archivo_central;
    }

    /**
     * Set the value of tipo_soporte.
     *
     * @param string $tipo_soporte
     * @return \App\Entity\TablaRetencion
     */
    public function setTipoSoporte($tipo_soporte)
    {
        $this->tipo_soporte = $tipo_soporte;

        return $this;
    }

    /**
     * Get the value of tipo_soporte.
     *
     * @return string
     */
    public function getTipoSoporte()
    {
        return $this->tipo_soporte;
    }

    /**
     * Set the value of disposicion_final_borrar.
     *
     * @param boolean $disposicion_final_borrar
     * @return \App\Entity\TablaRetencion
     */
    public function setDisposicionFinalBorrar($disposicion_final_borrar)
    {
        $this->disposicion_final_borrar = $disposicion_final_borrar;

        return $this;
    }

    /**
     * Get the value of disposicion_final_borrar.
     *
     * @return boolean
     */
    public function getDisposicionFinalBorrar()
    {
        return $this->disposicion_final_borrar;
    }

    /**
     * Set the value of disposicion_final_conservacion_total.
     *
     * @param boolean $disposicion_final_conservacion_total
     * @return \App\Entity\TablaRetencion
     */
    public function setDisposicionFinalConservacionTotal($disposicion_final_conservacion_total)
    {
        $this->disposicion_final_conservacion_total = $disposicion_final_conservacion_total;

        return $this;
    }

    /**
     * Get the value of disposicion_final_conservacion_total.
     *
     * @return boolean
     */
    public function getDisposicionFinalConservacionTotal()
    {
        return $this->disposicion_final_conservacion_total;
    }

    /**
     * Set the value of disposicion_final_conservacion_digital.
     *
     * @param boolean $disposicion_final_conservacion_digital
     * @return \App\Entity\TablaRetencion
     */
    public function setDisposicionFinalConservacionDigital($disposicion_final_conservacion_digital)
    {
        $this->disposicion_final_conservacion_digital = $disposicion_final_conservacion_digital;

        return $this;
    }

    /**
     * Get the value of disposicion_final_conservacion_digital.
     *
     * @return boolean
     */
    public function getDisposicionFinalConservacionDigital()
    {
        return $this->disposicion_final_conservacion_digital;
    }

    /**
     * Set the value of disposicion_final_microfilmado.
     *
     * @param boolean $disposicion_final_microfilmado
     * @return \App\Entity\TablaRetencion
     */
    public function setDisposicionFinalMicrofilmado($disposicion_final_microfilmado)
    {
        $this->disposicion_final_microfilmado = $disposicion_final_microfilmado;

        return $this;
    }

    /**
     * Get the value of disposicion_final_microfilmado.
     *
     * @return boolean
     */
    public function getDisposicionFinalMicrofilmado()
    {
        return $this->disposicion_final_microfilmado;
    }

    /**
     * Set the value of disposicion_final_seleccion.
     *
     * @param boolean $disposicion_final_seleccion
     * @return \App\Entity\TablaRetencion
     */
    public function setDisposicionFinalSeleccion($disposicion_final_seleccion)
    {
        $this->disposicion_final_seleccion = $disposicion_final_seleccion;

        return $this;
    }

    /**
     * Get the value of disposicion_final_seleccion.
     *
     * @return boolean
     */
    public function getDisposicionFinalSeleccion()
    {
        return $this->disposicion_final_seleccion;
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
     * @return \App\Entity\TablaRetencion
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
     * Set the value of procedimiento_disposicion.
     *
     * @param string $procedimiento_disposicion
     * @return \App\Entity\TablaRetencion
     */
    public function setProcedimientoDisposicion($procedimiento_disposicion)
    {
        $this->procedimiento_disposicion = $procedimiento_disposicion;

        return $this;
    }

    /**
     * Get the value of procedimiento_disposicion.
     *
     * @return string
     */
    public function getProcedimientoDisposicion()
    {
        return $this->procedimiento_disposicion;
    }

    /**
     * Set the value of ley_normatividad.
     *
     * @param string $ley_normatividad
     * @return \App\Entity\TablaRetencion
     */
    public function setLeyNormatividad($ley_normatividad)
    {
        $this->ley_normatividad = $ley_normatividad;

        return $this;
    }

    /**
     * Get the value of ley_normatividad.
     *
     * @return string
     */
    public function getLeyNormatividad()
    {
        return $this->ley_normatividad;
    }

    /**
     * Set the value of modulo.
     *
     * @param string $modulo
     * @return \App\Entity\TablaRetencion
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;

        return $this;
    }

    /**
     * Get the value of modulo.
     *
     * @return string
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set the value of inicio_vigencia.
     *
     * @param \DateTime $inicio_vigencia
     * @return \App\Entity\TablaRetencion
     */
    public function setInicioVigencia($inicio_vigencia)
    {
        $this->inicio_vigencia = $inicio_vigencia;

        return $this;
    }

    /**
     * Get the value of inicio_vigencia.
     *
     * @return \DateTime
     */
    public function getInicioVigencia()
    {
        return $this->inicio_vigencia;
    }

    // /**
    //  * Set the value of fin_vigencia.
    //  *
    //  * @param \DateTime $fin_vigencia
    //  * @return \App\Entity\TablaRetencion
    //  */
    // public function setFinVigencia($fin_vigencia)
    // {
    //     $this->fin_vigencia = $fin_vigencia;

    //     return $this;
    // }

    // /**
    //  * Get the value of fin_vigencia.
    //  *
    //  * @return \DateTime
    //  */
    // public function getFinVigencia()
    // {
    //     return $this->fin_vigencia;
    // }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\TablaRetencion
     */
    public function setEstadoId($estado_id)
    {
        $this->estado_id = $estado_id;

        return $this;
    }

    /**
     * Get the value of estado_id.
     *
     * @return integer
     */
    public function getEstadoId()
    {
        return $this->estado_id;
    }

    /**
     * Set the value of estructura_documental_id.
     *
     * @param integer $estructura_documental_id
     * @return \App\Entity\TablaRetencion
     */
    public function setEstructuraDocumentalId($estructura_documental_id)
    {
        $this->estructura_documental_id = $estructura_documental_id;

        return $this;
    }

    /**
     * Get the value of estructura_documental_id.
     *
     * @return integer
     */
    public function getEstructuraDocumentalId()
    {
        return $this->estructura_documental_id;
    }

    /**
     * Add Valordocumental entity to collection (many to many).
     *
     * @param \App\Entity\ValorDocumental $valordocumental
     * @return \App\Entity\TablaRetencion
     */
    public function addValorDocumental(ValorDocumental $valordocumental)
    {
        $this->valorDocumentals[] = $valordocumental;

        return $this;
    }

    /**
     * Remove Valordocumental entity from collection (many to many).
     *
     * @param \App\Entity\ValordocumentalTrd $valordocumental
     * @return \App\Entity\TablaRetencion
     */
    public function removeValorDocumental(Valordocumental $valordocumental)
    {
        $this->valorDocumentals->removeElement($valordocumental);

        return $this;
    }

    /**
     * Get ValordocumentalTrd entity collection (many to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValorDocumentals()
    {
        return $this->valorDocumentals;
    }

    /**
     * Set EstructuraDocumental entity (one to one).
     *
     * @param \App\Entity\EstructuraDocumental $estructuraDocumental
     * @return \App\Entity\TablaRetencion
     */
    public function setEstructuraDocumental(EstructuraDocumental $estructuraDocumental)
    {
        $this->estructuraDocumental = $estructuraDocumental;

        return $this;
    }

    /**
     * Get EstructuraDocumental entity (one to one).
     *
     * @return \App\Entity\EstructuraDocumental
     */
    public function getEstructuraDocumental()
    {
        return $this->estructuraDocumental;
    }

    /**
     * Get the value of transferencia_medio_electronico
     */
    public function getTransferenciaMedioElectronico()
    {
        return $this->transferencia_medio_electronico;
    }

    /**
     * Set the value of transferencia_medio_electronico
     *
     * @return  self
     */
    public function setTransferenciaMedioElectronico($transferencia_medio_electronico)
    {
        $this->transferencia_medio_electronico = $transferencia_medio_electronico;

        return $this;
    }
    /**
     * Get the value of direccion_documentos_almacenados_electronicamente
     */
    public function getDireccionDocumentosAlmacenadosElectronicamente()
    {
        return $this->direccion_documentos_almacenados_electronicamente;
    }

    /**
     * Set the value of direccion_documentos_almacenados_electronicamente
     *
     * @return  self
     */
    public function setDireccionDocumentosAlmacenadosElectronicamente($direccion_documentos_almacenados_electronicamente)
    {
        $this->direccion_documentos_almacenados_electronicamente = $direccion_documentos_almacenados_electronicamente;

        return $this;
    }

    /**
     * Get the value of fecha_version
     */
    public function getFechaVersion()
    {
        return $this->fecha_version;
    }

    /**
     * Set the value of fecha_version
     *
     * @return  self
     */
    public function setFechaVersion($fecha_version)
    {
        $this->fecha_version = $fecha_version;

        return $this;
    }

    /**
     * Set the value of has_change.
     *
     * @param boolean $has_change
     * @return \App\Entity\TablaRetencion
     */
    public function setHasChange($has_change)
    {
        $this->has_change = $has_change;

        return $this;
    }

    /**
     * Get the value of has_change.
     *
     * @return boolean
     */
    public function getHasChange()
    {
        return $this->has_change;
    }

    public function __sleep()
    {
        return array('id', 'codigo_archivo_documental', 'version', 'descripcion', 'tiempo_retencion_archivo_gestion', 'unidad_retencion_archivo_gestion', 'tiempo_retencion_archivo_central', 'unidad_retencion_archivo_central', 'tipo_soporte', 'disposicion_final_borrar', 'disposicion_final_conservacion_total', 'disposicion_final_conservacion_digital', 'disposicion_final_microfilmado', 'disposicion_final_seleccion', 'procedimiento_disposicion', 'ley_normatividad', 'modulo', 'inicio_vigencia', 'estado_id', 'tipo_documental_id', 'estructura_documental_id', 'transferencia_medios_electronicos', 'direccion_documentos_almacenados_electronicamente', 'fecha_version'); //'fin_vigencia',
    }

}
