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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\RetentionTableCreate;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
 *          "method"="POST",
 *          "path"="/tabla_retencion_version",
 *          "controller"=RetentionTableCreate::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "getEspecial"={
 *         "method"="GET",
 *         "path"="/tabla_retencion_version/special",
 *         "controller"=TablaRetencionGetEspecial::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "generateversion"={
 *         "method"="GET",
 *         "path"="/tabla_retencion_version/generateversion",
 *         "controller"=GenerarVersion::class,
 *      },
 *      "getversions"={
 *         "method"="GET",
 *         "path"="/tabla_retencion_version/getversions",
 *         "controller"=GetVersions::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "getcurrentversion"={
 *         "method"="GET",
 *         "path"="/tabla_retencion_version/getcurrentversion",
 *         "controller"=CurrentVersion::class,
 *      },
 *      "import"={
 *         "method"="POST",
 *         "path"="/tabla_retencion_version/import",
 *         "controller"=TablaRetencionImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/tabla_retencion_version/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *     "update"={
 *         "method"="PUT",
 *         "path"="/tabla_retencion_version/{id}/valordocumentals",
 *         "controller"=TablaRetencionUpdateSpecial::class
 *     },
 *      "findOndeByIdEspecial"={
 *         "method"="GET",
 *         "path"="/tabla_retencion_version/{id}/special",
 *         "controller"=TablaRetencionFindOneByIdEspecial::class
 *      },
 *  }
 * )
 * App\Entity\TablaRetencionVersion
 *
 * @ORM\Entity(repositoryClass="App\Repository\TablaRetencionVersionRepository")
 * @ORM\Table(name="tabla_retencion_version", indexes={@ORM\Index(name="fk_tabla_retencion_version_tipo_documental1_idx", columns={"tipo_documental_id"}), @ORM\Index(name="fk_tabla_retencion_version_estructura_documental_version1_idx", columns={"estructura_documental_version_id"})})
 */
class TablaRetencionVersion
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

    protected $estructura_documental_version_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tipo_documental_id;

    /**
     * Muchas TRDs tienen muchos Valores Documentales
     * @ORM\ManyToMany(targetEntity="ValorDocumental", inversedBy="tablaRetencionsVersion")
     * @ORM\JoinTable(
     *  name="trd_version_valordocumental",
     *  joinColumns={
     *      @ORM\JoinColumn(name="tabla_retencion_version_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="valordocumental_id", referencedColumnName="id")
     *  }
     * )
     */

    protected $valorDocumentals;

    /**
     * @ORM\ManyToOne(targetEntity="TipoDocumental", inversedBy="tablaRetencionsVersion")
     * @ORM\JoinColumn(name="tipo_documental_id", referencedColumnName="id", nullable=false)
     */
    protected $tipoDocumental;

    /**
     * @ORM\OneToOne(targetEntity="EstructuraDocumentalVersion")
     * @ORM\JoinColumn(name="estructura_documental_version_id", referencedColumnName="id", nullable=false)
     */
    protected $estructuraDocumentalVersion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $tabla_retencion_id;

    public function __construct() {
        $this->valorDocumentals = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
    //  * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * Set the value of tipo_documental_id.
     *
     * @param integer $tipo_documental_id
     * @return \App\Entity\TablaRetencionVersion
     */
    public function setTipoDocumentalId($tipo_documental_id)
    {
        $this->tipo_documental_id = $tipo_documental_id;

        return $this;
    }

    /**
     * Get the value of tipo_documental_id.
     *
     * @return integer
     */
    public function getTipoDocumentalId()
    {
        return $this->tipo_documental_id;
    }

    /**
     * Set the value of estructura_documental_version_id.
     *
     * @param integer $estructura_documental_version_id
     * @return \App\Entity\TablaRetencionVersion
     */
    public function setEstructuraDocumentalVersionId($estructura_documental_version_id)
    {
        $this->estructura_documental_version_id = $estructura_documental_version_id;

        return $this;
    }

    /**
     * Get the value of estructura_documental_version_id.
     *
     * @return integer
     */
    public function getEstructuraDocumentalVersionId()
    {
        return $this->estructura_documental_version_id;
    }

    /**
     * Set TablaRetencionVersion entity (one to one).
     *
     * @param \App\Entity\TablaRetencionVersion $TablaRetencionVersion
     * @return \App\Entity\TablaRetencionVersion
     */
    public function setTablaRetencionVersion(TablaRetencionVersion $TablaRetencionVersion = null)
    {
        $this->TablaRetencionVersion = $TablaRetencionVersion;

        return $this;
    }

    /**
     * Get TablaRetencionVersion entity (one to one).
     *
     * @return \App\Entity\TablaRetencionVersion
     */
    public function getTablaRetencionVersion()
    {
        return $this->TablaRetencionVersion;
    }

    /**
     * Add Valordocumental entity to collection (many to many).
     *
     * @param \App\Entity\ValorDocumental $valordocumental
     * @return \App\Entity\TablaRetencionVersion
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
     * @return \App\Entity\TablaRetencionVersion
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
     * Set TipoDocumental entity (many to one).
     *
     * @param \App\Entity\TipoDocumental $tipoDocumental
     * @return \App\Entity\TablaRetencionVersion
     */
    public function setTipoDocumental(TipoDocumental $tipoDocumental = null)
    {
        $this->tipoDocumental = $tipoDocumental;

        return $this;
    }

    /**
     * Get TipoDocumental entity (many to one).
     *
     * @return \App\Entity\TipoDocumental
     */
    public function getTipoDocumental()
    {
        return $this->tipoDocumental;
    }

    /**
     * Set EstructuraDocumentalVersion entity (one to one).
     *
     * @param \App\Entity\EstructuraDocumentalVersion $estructuraDocumentalVersion
     * @return \App\Entity\TablaRetencionVersion
     */
    public function setEstructuraDocumentalVersion(EstructuraDocumentalVersion $estructuraDocumentalVersion)
    {
        $this->estructuraDocumentalVersion = $estructuraDocumentalVersion;

        return $this;
    }

    /**
     * Get EstructuraDocumentalVersion entity (one to one).
     *
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function getEstructuraDocumentalVersion()
    {
        return $this->estructuraDocumentalVersion;
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
     * Set the value of tabla_retencion_id.
     *
     * @param integer $tabla_retencion_id
     * @return \App\Entity\TablaRetencionVersion
     */
    public function setTablaRetencionId($tabla_retencion_id)
    {
        $this->tabla_retencion_id = $tabla_retencion_id;

        return $this;
    }

    /**
     * Get the value of tabla_retencion_id.
     *
     * @return integer
     */
    public function getTablaRetencionId()
    {
        return $this->tabla_retencion_id;
    }

    public function __sleep()
    {
        return array('id', 'codigo_archivo_documental', 'version', 'descripcion', 'tiempo_retencion_archivo_gestion', 'unidad_retencion_archivo_gestion', 'tiempo_retencion_archivo_central', 'unidad_retencion_archivo_central', 'tipo_soporte', 'disposicion_final_borrar', 'disposicion_final_conservacion_total', 'disposicion_final_conservacion_digital', 'disposicion_final_microfilmado', 'disposicion_final_seleccion', 'procedimiento_disposicion', 'ley_normatividad', 'modulo', 'inicio_vigencia', 'estado_id', 'tipo_documental_id', 'estructura_documental_version_id', 'transferencia_medios_electronicos', 'direccion_documentos_almacenados_electronicamente', 'fecha_version'); //'fin_vigencia',
    }

}
