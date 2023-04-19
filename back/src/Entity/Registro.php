<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\ArchivoRegistro;
use App\Controller\AuditoriaRegistro;
use App\Controller\NotificadosByRegistro;
use App\Controller\PlantillasFormularioVersion;
use App\Controller\PlantillasPasoVersion;
use App\Controller\RegistroArchivo;
use App\Controller\RegistroFormularioVersion;
use App\Controller\RegistroPlantillaMixer;
use App\Controller\RegistryByUser;
use App\Controller\RegistryCredentials;
use App\Controller\RegistryHeader;
use App\Controller\RelacionadosByRegistro;
use App\Controller\StickerPrinter;
use App\Controller\StickerViewer;
use App\Controller\Registro\ValoresCamposByRegistro;
use App\Controller\RegistroFormato;
use App\Controller\Registro\RegistroByEjecucionFlujoId;
use App\Controller\Registro\RegistroEjecucionFlujo;
use App\Controller\Registro\RegistroRadicado;
use App\Controller\Registro\RegistroPorId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\Registro\ExistRegistroByEjecucionPasoId;


/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "registrybyuser"={
 *          "method"="GET",
 *          "path"="/registros/byuser",
 *          "controller"=RegistryByUser::class,
 *          "defaults"={
 *                  "_items_per_page"=10,
 *          }
 *      },
 *      "guardararchivoregistro"={
 *          "method"="POST",
 *          "path"="/registros/{id}/savefile",
 *          "controller"=ArchivoRegistro::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "registroformulario"={
 *          "method"="POST",
 *          "path"="/registros/save",
 *          "controller"=RegistroFormularioVersion::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "getExistRegistroByEjecucionFlujoId"={
 *         "method"="GET",
 *         "path"="/registros/{id}/ejecucion_flujo",
 *         "controller"=RegistroByEjecucionFlujoId::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *      "getRegistroByEjecucionFlujo"={
 *         "method"="GET",
 *         "path"="/registros/{id}/registro_flujo",
 *         "controller"=RegistroEjecucionFlujo::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *      "getRegistroByFiling"={
 *         "method"="GET",
 *         "path"="/registros/{radicado}/radicado",
 *         "controller"=RegistroRadicado::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *      "getRegistroById"={
 *         "method"="GET",
 *         "path"="/registros/{id}/registro-id",
 *         "controller"=RegistroPorId::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/registros/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "getsticker"={
 *          "method"="GET",
 *          "path"="/registros/{id}/sticker",
 *          "controller"=StickerViewer::class
 *      },
 *      "printsticker"={
 *          "method"="GET",
 *          "path"="/registros/{id}/sticker/print",
 *          "controller"=StickerPrinter::class,
 *      },
 *      "mixregistroplantilla"={
 *          "method"="GET",
 *          "path"="/registros/{id}/plantillas/{plantilla_id}/mix",
 *          "controller"=RegistroPlantillaMixer::class
 *      },
 *      "getnotificadosbyregistro"={
 *          "method"="GET",
 *          "path"="/registros/{id}/notificados",
 *          "controller"=NotificadosByRegistro::class
 *      },
 *      "getrelacionadosbyregistro"={
 *          "method"="GET",
 *          "path"="/registros/{id}/relacionados",
 *          "controller"=RelacionadosByRegistro::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "getvalorescampos"={
 *          "method"="GET",
 *          "path"="/registros/{id}/campos",
 *          "controller"=ValoresCamposByRegistro::class
 *      },
 *      "registrycredentials"={
 *          "method"="GET",
 *          "path"="/registros/{id}/credentials",
 *          "controller"=RegistryCredentials::class
 *      },
 *      "getheader"={
 *          "method"="GET",
 *          "path"="/registros/{id}/header",
 *          "controller"=RegistryHeader::class
 *      },
 *      "getAuditoriaRegistro"={
 *         "method"="GET",
 *         "path"="/registros/{id}/auditoria",
 *         "controller"=AuditoriaRegistro::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "getRegistroArchivo"={
 *         "method"="GET",
 *         "path"="/registros/{id}/archivos",
 *         "controller"=RegistroArchivo::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },*
 *      "getPlantillasFormulario"={
 *         "method"="GET",
 *         "path"="/registros/{id}/formatos",
 *         "controller"=RegistroFormato::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *       },
 *      "getFormatosFormulario"={
 *         "method"="GET",
 *         "path"="/registros/{id}/formularioVersion/plantillas",
 *         "controller"=PlantillasFormularioVersion::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *       },
 *       "getFormatosPaso"={
 *         "method"="GET",
 *         "path"="/registros/{id}/pasoVersion/plantillas",
 *         "controller"=PlantillasPasoVersion::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *      "getExistRegistroByEjecucionPasoId"={
 *         "method"="GET",
 *         "path"="/registros/{id}/exist_registro",
 *         "controller"=ExistRegistroByEjecucionPasoId::class,
 *         "defaults"={"_api_receive"=false}
 *       }
 *  }
 * )
 * App\Entity\Registro
 *
 * @ORM\Entity(repositoryClass="App\Repository\RegistroRepository")
 * @ORM\Table(name="registro", indexes={@ORM\Index(name="fk_Registro_formulario_version1_idx", columns={"formulario_version_id"}), @ORM\Index(name="fk_registro_usuario1_idx", columns={"usuario_id"})})
 */
class Registro
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
    protected $radicacion_year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $radicacion_counter;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_hora;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_sticker;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $resumen;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $busqueda;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $formulario_version_id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha_formulario_version;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $nombre_formulario;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $sede;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tipo_correspondencia;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $consecutivo;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $nomenclatura_formulario;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $usuario_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $start_work_flow;

    /**
     * @ORM\OneToMany(targetEntity="Archivo", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $archivos;

    /**
     * @ORM\OneToMany(targetEntity="Compartido", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $compartidos;

    /**
     * @ORM\OneToMany(targetEntity="Formato", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $formatosVersion;

    /**
     * @ORM\OneToMany(targetEntity="Notificacion", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $notificacions;

    /**
     * @ORM\OneToMany(targetEntity="RegistroCampo", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroCampos;

    /**
     * @ORM\OneToMany(targetEntity="RegistroEntidad", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroEntidads;

    /**
     * @ORM\OneToMany(targetEntity="RegistroFecha", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroFechas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroHora", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroHoras;

    /**
     * @ORM\OneToMany(targetEntity="RegistroLista", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroListas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroMultiseleccion", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroMultiseleccions;

    /**
     * @ORM\OneToMany(targetEntity="RegistroNumericoDecimal", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroNumericoDecimals;

    /**
     * @ORM\OneToMany(targetEntity="RegistroNumericoEntero", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroNumericoEnteros;

    /**
     * @ORM\OneToMany(targetEntity="RegistroBooleano", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroBooleanos;

    /**
     * @ORM\OneToMany(targetEntity="RegistroNumericoMoneda", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroNumericoMonedas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroTextoCorto", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroTextoCortos;

    /**
     * @ORM\OneToMany(targetEntity="RegistroTextoLargo", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     */
    protected $registroTextoLargos;

    /**
     * @ORM\ManyToOne(targetEntity="FormularioVersion", inversedBy="registros")
     * @ORM\JoinColumn(name="formulario_version_id", referencedColumnName="id", nullable=true)
     * @ApiSubresource(maxDepth=1)
     */
    protected $formularioVersion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="registros")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     */
    protected $usuario;

    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="registro")
     * @ORM\JoinColumn(name="id", referencedColumnName="registro_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $comentarios;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     */
    protected $radicado;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ejecucion_paso_id;

    /**
     * @ORM\OneToOne(targetEntity="EjecucionPaso")
     * @ORM\JoinColumn(name="ejecucion_paso_id", referencedColumnName="id", nullable=true)
     */
    protected $ejecucionPaso;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ejecucion_flujo_id;

    /**
     * @ORM\OneToOne(targetEntity="EjecucionFlujo")
     * @ORM\JoinColumn(name="ejecucion_flujo_id", referencedColumnName="id", nullable=true)
     */
    protected $ejecucionFlujo;

    public function __construct()
    {
        $this->archivos = new ArrayCollection();
        $this->compartidos = new ArrayCollection();
        $this->formatosVersion = new ArrayCollection();
        $this->notificacions = new ArrayCollection();
        $this->registroCampos = new ArrayCollection();
        $this->registroEntidads = new ArrayCollection();
        $this->registroFechas = new ArrayCollection();
        $this->registroHoras = new ArrayCollection();
        $this->registroListas = new ArrayCollection();
        $this->registroMultiseleccions = new ArrayCollection();
        $this->registroNumericoDecimals = new ArrayCollection();
        $this->registroNumericoEnteros = new ArrayCollection();
        $this->registroNumericoBooleanos = new ArrayCollection();
        $this->registroNumericoMonedas = new ArrayCollection();
        $this->registroTextoCortos = new ArrayCollection();
        $this->registroTextoLargos = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Registro
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
     * Set the value of radicacion_year.
     *
     * @param string $radicacion_year
     * @return \App\Entity\Registro
     */
    public function setRadicacionYear($radicacion_year)
    {
        $this->radicacion_year = $radicacion_year;

        return $this;
    }

    /**
     * Get the value of radicacion_year.
     *
     * @return string
     */
    public function getRadicacionYear()
    {
        return $this->radicacion_year;
    }

    /**
     * Set the value of radicacion_counter.
     *
     * @param string $radicacion_counter
     * @return \App\Entity\Registro
     */
    public function setRadicacionCounter($radicacion_counter)
    {
        $this->radicacion_counter = $radicacion_counter;

        return $this;
    }

    /**
     * Get the value of radicacion_counter.
     *
     * @return string
     */
    public function getRadicacionCounter()
    {
        return $this->radicacion_counter;
    }
    /**
     * Set the value of fecha_hora.
     *
     * @param \DateTime $fecha_hora
     * @return \App\Entity\Registro
     */
    public function setFechaHora($fecha_hora)
    {
        $this->fecha_hora = $fecha_hora;

        return $this;
    }

    /**
     * Get the value of fecha_hora.
     *
     * @return \DateTime
     */
    public function getFechaHora()
    {
        return $this->fecha_hora;
    }

    /**
     * Set the value of fecha_sticker.
     *
     * @param \DateTime $fecha_sticker
     * @return \App\Entity\Registro
     */
    public function setFechaSticker($fecha_sticker)
    {
        $this->fecha_sticker = $fecha_sticker;

        return $this;
    }

    /**
     * Get the value of fecha_sticker.
     *
     * @return \DateTime
     */
    public function getFechaSticker()
    {
        return $this->fecha_sticker;
    }

    /**
     * Set the value of resumen.
     *
     * @param string $resumen
     * @return \App\Entity\Registro
     */
    public function setResumen($resumen)
    {
        $this->resumen = $resumen;

        return $this;
    }

    /**
     * Get the value of resumen.
     *
     * @return string
     */
    public function getResumen()
    {
        return $this->resumen;
    }

    /**
     * Set the value of busqueda.
     *
     * @param string $busqueda
     * @return \App\Entity\Registro
     */
    public function setBusqueda($busqueda)
    {
        $this->busqueda = $busqueda;

        return $this;
    }

    /**
     * Get the value of busqueda.
     *
     * @return string
     */
    public function getBusqueda()
    {
        return $this->busqueda;
    }

    /**
     * Set the value of formulario_version_id.
     *
     * @param integer $formulario_version_id
     * @return \App\Entity\Registro
     */
    public function setFormularioVersionId($formulario_version_id)
    {
        $this->formulario_version_id = $formulario_version_id;

        return $this;
    }

    /**
     * Get the value of formulario_version_id.
     *
     * @return integer
     */
    public function getFormularioVersionId()
    {
        return $this->formulario_version_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Registro
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
     * Set the value of usuario_id.
     *
     * @param integer $usuario_id
     * @return \App\Entity\Registro
     */
    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }

    /**
     * Get the value of usuario_id.
     *
     * @return integer
     */
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    /**
     * Set the value of start_work_flow.
     *
     * @param integer $start_work_flow
     * @return \App\Entity\Registro
     */
    public function setStartWorkFlow($start_work_flow)
    {
        $this->start_work_flow = $start_work_flow;

        return $this;
    }

    /**
     * Get the value of start_work_flow.
     *
     * @return boolean
     */
    public function getStartWorkFlow()
    {
        return $this->start_work_flow;
    }

    /**
     * Add Archivo entity to collection (one to many).
     *
     * @param \App\Entity\Archivo $archivo
     * @return \App\Entity\Registro
     */
    public function addArchivo(Archivo $archivo)
    {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove Archivo entity from collection (one to many).
     *
     * @param \App\Entity\Archivo $archivo
     * @return \App\Entity\Registro
     */
    public function removeArchivo(Archivo $archivo)
    {
        $this->archivos->removeElement($archivo);

        return $this;
    }

    /**
     * Get Archivo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArchivos()
    {
        return $this->archivos;
    }

    /**
     * Add Compartido entity to collection (one to many).
     *
     * @param \App\Entity\Compartido $compartido
     * @return \App\Entity\Registro
     */
    public function addCompartido(Compartido $compartido)
    {
        $this->compartidos[] = $compartido;

        return $this;
    }

    /**
     * Remove Compartido entity from collection (one to many).
     *
     * @param \App\Entity\Compartido $compartido
     * @return \App\Entity\Registro
     */
    public function removeCompartido(Compartido $compartido)
    {
        $this->compartidos->removeElement($compartido);

        return $this;
    }

    /**
     * Get Compartido entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompartidos()
    {
        return $this->compartidos;
    }

    /**
     * Add FormatoVersion entity to collection (one to many).
     *
     * @param \App\Entity\FormatoVersion $formatoVersion
     * @return \App\Entity\Registro
     */
    public function addFormatoVersion(FormatoVersion $formatoVersion)
    {
        $this->formatosVersion[] = $formatoVersion;

        return $this;
    }

    /**
     * Remove FormatoVersion entity from collection (one to many).
     *
     * @param \App\Entity\FormatoVersion $formato
     * @return \App\Entity\Registro
     */
    public function removeFormatoVersion(FormatoVersion $formatoVersion)
    {
        $this->formatosVersion->removeElement($formatoVersion);

        return $this;
    }

    /**
     * Get FormatoVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormatosVersion()
    {
        return $this->formatosVersion;
    }

    /**
     * Add Notificacion entity to collection (one to many).
     *
     * @param \App\Entity\Notificacion $notificacion
     * @return \App\Entity\Registro
     */
    public function addNotificacion(Notificacion $notificacion)
    {
        $this->notificacions[] = $notificacion;

        return $this;
    }

    /**
     * Remove Notificacion entity from collection (one to many).
     *
     * @param \App\Entity\Notificacion $notificacion
     * @return \App\Entity\Registro
     */
    public function removeNotificacion(Notificacion $notificacion)
    {
        $this->notificacions->removeElement($notificacion);

        return $this;
    }

    /**
     * Get Notificacion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificacions()
    {
        return $this->notificacions;
    }

    /**
     * Add RegistroCampo entity to collection (one to many).
     *
     * @param \App\Entity\RegistroCampo $registroCampo
     * @return \App\Entity\Registro
     */
    public function addRegistroCampo(RegistroCampo $registroCampo)
    {
        $this->registroCampos[] = $registroCampo;

        return $this;
    }

    /**
     * Remove RegistroCampo entity from collection (one to many).
     *
     * @param \App\Entity\RegistroCampo $registroCampo
     * @return \App\Entity\Registro
     */
    public function removeRegistroCampo(RegistroCampo $registroCampo)
    {
        $this->registroCampos->removeElement($registroCampo);

        return $this;
    }

    /**
     * Get RegistroCampo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroCampos()
    {
        return $this->registroCampos;
    }

    /**
     * Add RegistroEntidad entity to collection (one to many).
     *
     * @param \App\Entity\RegistroEntidad $registroEntidad
     * @return \App\Entity\Registro
     */
    public function addRegistroEntidad(RegistroEntidad $registroEntidad)
    {
        $this->registroEntidads[] = $registroEntidad;

        return $this;
    }

    /**
     * Remove RegistroEntidad entity from collection (one to many).
     *
     * @param \App\Entity\RegistroEntidad $registroEntidad
     * @return \App\Entity\Registro
     */
    public function removeRegistroEntidad(RegistroEntidad $registroEntidad)
    {
        $this->registroEntidads->removeElement($registroEntidad);

        return $this;
    }

    /**
     * Get RegistroEntidad entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroEntidads()
    {
        return $this->registroEntidads;
    }

    /**
     * Add RegistroFecha entity to collection (one to many).
     *
     * @param \App\Entity\RegistroFecha $registroFecha
     * @return \App\Entity\Registro
     */
    public function addRegistroFecha(RegistroFecha $registroFecha)
    {
        $this->registroFechas[] = $registroFecha;

        return $this;
    }

    /**
     * Remove RegistroFecha entity from collection (one to many).
     *
     * @param \App\Entity\RegistroFecha $registroFecha
     * @return \App\Entity\Registro
     */
    public function removeRegistroFecha(RegistroFecha $registroFecha)
    {
        $this->registroFechas->removeElement($registroFecha);

        return $this;
    }

    /**
     * Get RegistroFecha entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroFechas()
    {
        return $this->registroFechas;
    }

    /**
     * Add RegistroHora entity to collection (one to many).
     *
     * @param \App\Entity\RegistroHora $registroHora
     * @return \App\Entity\Registro
     */
    public function addRegistroHora(RegistroHora $registroHora)
    {
        $this->registroHoras[] = $registroHora;

        return $this;
    }

    /**
     * Remove RegistroHora entity from collection (one to many).
     *
     * @param \App\Entity\RegistroHora $registroHora
     * @return \App\Entity\Registro
     */
    public function removeRegistroHora(RegistroHora $registroHora)
    {
        $this->registroHoras->removeElement($registroHora);

        return $this;
    }

    /**
     * Get RegistroHora entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroHoras()
    {
        return $this->registroHoras;
    }

    /**
     * Add RegistroLista entity to collection (one to many).
     *
     * @param \App\Entity\RegistroLista $registroLista
     * @return \App\Entity\Registro
     */
    public function addRegistroLista(RegistroLista $registroLista)
    {
        $this->registroListas[] = $registroLista;

        return $this;
    }

    /**
     * Remove RegistroLista entity from collection (one to many).
     *
     * @param \App\Entity\RegistroLista $registroLista
     * @return \App\Entity\Registro
     */
    public function removeRegistroLista(RegistroLista $registroLista)
    {
        $this->registroListas->removeElement($registroLista);

        return $this;
    }

    /**
     * Get RegistroLista entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroListas()
    {
        return $this->registroListas;
    }

    /**
     * Add RegistroMultiseleccion entity to collection (one to many).
     *
     * @param \App\Entity\RegistroMultiseleccion $registroMultiseleccion
     * @return \App\Entity\Registro
     */
    public function addRegistroMultiseleccion(RegistroMultiseleccion $registroMultiseleccion)
    {
        $this->registroMultiseleccions[] = $registroMultiseleccion;

        return $this;
    }

    /**
     * Remove RegistroMultiseleccion entity from collection (one to many).
     *
     * @param \App\Entity\RegistroMultiseleccion $registroMultiseleccion
     * @return \App\Entity\Registro
     */
    public function removeRegistroMultiseleccion(RegistroMultiseleccion $registroMultiseleccion)
    {
        $this->registroMultiseleccions->removeElement($registroMultiseleccion);

        return $this;
    }

    /**
     * Get RegistroMultiseleccion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroMultiseleccions()
    {
        return $this->registroMultiseleccions;
    }

    /**
     * Add RegistroNumericoDecimal entity to collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoDecimal $registroNumericoDecimal
     * @return \App\Entity\Registro
     */
    public function addRegistroNumericoDecimal(RegistroNumericoDecimal $registroNumericoDecimal)
    {
        $this->registroNumericoDecimals[] = $registroNumericoDecimal;

        return $this;
    }

    /**
     * Remove RegistroNumericoDecimal entity from collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoDecimal $registroNumericoDecimal
     * @return \App\Entity\Registro
     */
    public function removeRegistroNumericoDecimal(RegistroNumericoDecimal $registroNumericoDecimal)
    {
        $this->registroNumericoDecimals->removeElement($registroNumericoDecimal);

        return $this;
    }

    /**
     * Get RegistroNumericoDecimal entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroNumericoDecimals()
    {
        return $this->registroNumericoDecimals;
    }

    /**
     * Add RegistroNumericoEntero entity to collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoEntero $registroNumericoEntero
     * @return \App\Entity\Registro
     */
    public function addRegistroNumericoEntero(RegistroNumericoEntero $registroNumericoEntero)
    {
        $this->registroNumericoEnteros[] = $registroNumericoEntero;

        return $this;
    }

    /**
     * Remove RegistroNumericoEntero entity from collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoEntero $registroNumericoEntero
     * @return \App\Entity\Registro
     */
    public function removeRegistroNumericoEntero(RegistroNumericoEntero $registroNumericoEntero)
    {
        $this->registroNumericoEnteros->removeElement($registroNumericoEntero);

        return $this;
    }

    /**
     * Get RegistroNumericoEntero entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroNumericoEnteros()
    {
        return $this->registroNumericoEnteros;
    }

    /**
     * Add RegistroNumericoMoneda entity to collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoMoneda $registroNumericoMoneda
     * @return \App\Entity\Registro
     */
    public function addRegistroNumericoMoneda(RegistroNumericoMoneda $registroNumericoMoneda)
    {
        $this->registroNumericoMonedas[] = $registroNumericoMoneda;

        return $this;
    }

    /**
     * Remove RegistroNumericoMoneda entity from collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoMoneda $registroNumericoMoneda
     * @return \App\Entity\Registro
     */
    public function removeRegistroNumericoMoneda(RegistroNumericoMoneda $registroNumericoMoneda)
    {
        $this->registroNumericoMonedas->removeElement($registroNumericoMoneda);

        return $this;
    }

    /**
     * Get RegistroNumericoMoneda entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroNumericoMonedas()
    {
        return $this->registroNumericoMonedas;
    }

    /**
     * Add RegistroTextoCorto entity to collection (one to many).
     *
     * @param \App\Entity\RegistroTextoCorto $registroTextoCorto
     * @return \App\Entity\Registro
     */
    public function addRegistroTextoCorto(RegistroTextoCorto $registroTextoCorto)
    {
        $this->registroTextoCortos[] = $registroTextoCorto;

        return $this;
    }

    /**
     * Remove RegistroTextoCorto entity from collection (one to many).
     *
     * @param \App\Entity\RegistroTextoCorto $registroTextoCorto
     * @return \App\Entity\Registro
     */
    public function removeRegistroTextoCorto(RegistroTextoCorto $registroTextoCorto)
    {
        $this->registroTextoCortos->removeElement($registroTextoCorto);

        return $this;
    }

    /**
     * Get RegistroTextoCorto entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroTextoCortos()
    {
        return $this->registroTextoCortos;
    }

    /**
     * Add RegistroTextoLargo entity to collection (one to many).
     *
     * @param \App\Entity\RegistroTextoLargo $registroTextoLargo
     * @return \App\Entity\Registro
     */
    public function addRegistroTextoLargo(RegistroTextoLargo $registroTextoLargo)
    {
        $this->registroTextoLargos[] = $registroTextoLargo;

        return $this;
    }

    /**
     * Remove RegistroTextoLargo entity from collection (one to many).
     *
     * @param \App\Entity\RegistroTextoLargo $registroTextoLargo
     * @return \App\Entity\Registro
     */
    public function removeRegistroTextoLargo(RegistroTextoLargo $registroTextoLargo)
    {
        $this->registroTextoLargos->removeElement($registroTextoLargo);

        return $this;
    }

    /**
     * Get RegistroTextoLargo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroTextoLargos()
    {
        return $this->registroTextoLargos;
    }

    /**
     * Add RegistroBooleano entity to collection (one to many).
     *
     * @param \App\Entity\RegistroBooleano $registroBooleano
     * @return \App\Entity\Registro
     */
    public function addRegistroBooleano(RegistroBooleano $registroBooleano)
    {
        $this->registroBooleanos[] = $registroBooleano;

        return $this;
    }

    /**
     * Remove RegistroBooleano entity from collection (one to many).
     *
     * @param \App\Entity\RegistroBooleano $registroBooleano
     * @return \App\Entity\Registro
     */
    public function removeRegistroBooleano(RegistroBooleano $registroBooleano)
    {
        $this->registroBooleanos->removeElement($registroBooleano);

        return $this;
    }

    /**
     * Get RegistroBooleano entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroBooleanos()
    {
        return $this->registroBooleanos;
    }

    /**
     * Set FormularioVersion entity (many to one).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\Registro
     */
    public function setFormularioVersion(FormularioVersion $formularioVersion = null)
    {
        $this->formularioVersion = $formularioVersion;

        return $this;
    }

    /**
     * Get FormularioVersion entity (many to one).
     *
     * @return \App\Entity\FormularioVersion
     */
    public function getFormularioVersion()
    {
        return $this->formularioVersion;
    }

    /**
     * Set Usuario entity (many to one).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Registro
     */
    public function setUsuario(Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get Usuario entity (many to one).
     *
     * @return \App\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set the value of fecha_formulario_version.
     *
     * @param \DateTime $fecha_formulario_version
     * @return \App\Entity\Registro
     */
    public function setFechaFormularioVersion($fecha_formulario_version)
    {
        $this->fecha_formulario_version = $fecha_formulario_version;

        return $this;
    }

    /**
     * Get the value of fecha_version.
     *
     * @return \DateTime
     */
    public function getFechaFormularioVersion()
    {
        return $this->fecha_formulario_version;
    }

    /**
     * Set the value of nombre.
     *
     * @param string $nombre_formulario
     * @return \App\Entity\Registro
     */
    public function setNombreFormulario($nombre_formulario)
    {
        $this->nombre_formulario = $nombre_formulario;

        return $this;
    }

    /**
     * Get the value of nombre_formulario.
     *
     * @return string
     */
    public function getNombreFormulario()
    {
        return $this->nombre_formulario;
    }

    /**
     * Set the value of nomenclatura_formulario.
     *
     * @param string $nomenclatura_formulario
     * @return \App\Entity\Registro
     */
    public function setNomenclaturaFormulario($nomenclatura_formulario)
    {
        $this->nomenclatura_formulario = $nomenclatura_formulario;

        return $this;
    }

    /**
     * Get the value of nomenclatura_formulario.
     *
     * @return string
     */
    public function getNomenclaturaFormulario()
    {
        return $this->nomenclatura_formulario;
    }

    /**
     * Add comentario entity to collection (one to many).
     *
     * @param \App\Entity\Comentario $comentario
     * @return \App\Entity\Registro
     */
    public function addComentario(Comentario $comentario)
    {
        $this->comentarios[] = $comentario;

        return $this;
    }

    /**
     * Remove comentario entity from collection (one to many).
     *
     * @param \App\Entity\Comentario $comentario
     * @return \App\Entity\Registro
     */
    public function removeComentario(Comentario $comentario)
    {
        $this->comentarios->removeElement($comentario);

        return $this;
    }

    /**
     * Get comentarios entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /**
     * Get the value of sede
     * @return string
     */
    public function getSede()
    {
        return $this->sede;
    }

    /**
     * Set the value of sede
     *
     *
     * @param string $sede
     * @return \App\Entity\Registro
     */
    public function setSede($sede)
    {
        $this->sede = $sede;

        return $this;
    }

    /**
     * Get the value of tipo_correspondencia
     */
    public function getTipoCorrespondencia()
    {
        return $this->tipo_correspondencia;
    }

    /**
     * Set the value of tipo_correspondencia
     *
     * @return  self
     */
    public function setTipoCorrespondencia($tipo_correspondencia)
    {
        $this->tipo_correspondencia = $tipo_correspondencia;

        return $this;
    }

    /**
     * Get the value of consecutivo
     */
    public function getConsecutivo()
    {
        return $this->consecutivo;
    }

    /**
     * Set the value of consecutivo
     *
     * @return  self
     */
    public function setConsecutivo($consecutivo)
    {
        $this->consecutivo = $consecutivo;

        return $this;
    }

    /**
     * Set the value of radicado.
     *
     * @param string $radicado
     * @return \App\Entity\Registro
     */
    public function setRadicado($radicado)
    {
        $this->radicado = $radicado;

        return $this;
    }

    /**
     * Get the value of radicado.
     *
     * @return string
     */
    public function getRadicado()
    {
        return $this->radicado;
    }

    /**
     * Set the value of ejecucion_paso_id.
     *
     * @param integer $ejecucion_paso_id
     * @return \App\Entity\Registro
     */
    public function setEjecucionPasoId($ejecucion_paso_id)
    {
        $this->ejecucion_paso_id = $ejecucion_paso_id;

        return $this;
    }

    /**
     * Get the value of ejecucion_paso_id.
     *
     * @return integer
     */
    public function getEjecucionPasoId()
    {
        return $this->ejecucion_paso_id;
    }

    /**
     * Set EjecucionPaso entity (many to one).
     *
     * @param \App\Entity\EjecucionPaso $ejecucionPaso
     * @return \App\Entity\Registro
     */
    public function setEjecucionPaso(EjecucionPaso $ejecucionPaso = null)
    {
        $this->ejecucionPaso = $ejecucionPaso;

        return $this;
    }

    /**
     * Get EjecucionPaso entity (many to one).
     *
     * @return \App\Entity\EjecucionPaso
     */
    public function getEjecucionPaso()
    {
        return $this->ejecucionPaso;
    }

    /**
     * Set the value of ejecucion_flujo_id.
     *
     * @param integer $ejecucion_flujo_id
     * @return \App\Entity\Registro
     */
    public function setEjecucionFlujoId($ejecucion_flujo_id)
    {
        $this->ejecucion_flujo_id = $ejecucion_flujo_id;

        return $this;
    }

    /**
     * Get the value of ejecucion_flujo_id.
     *
     * @return integer
     */
    public function getEjecucionFlujoId()
    {
        return $this->ejecucion_flujo_id;
    }

    /**
     * Set EjecucionPaso entity (many to one).
     *
     * @param \App\Entity\EjecucionFlujo $ejecucionFlujo
     * @return \App\Entity\Registro
     */
    public function setEjecucionFlujo(EjecucionFlujo $ejecucionFlujo = null)
    {
        $this->ejecucionFlujo = $ejecucionFlujo;

        return $this;
    }

    /**
     * Get EjecucionFlujo entity (many to one).
     *
     * @return \App\Entity\EjecucionFlujo
     */
    public function getEjecucionFlujo()
    {
        return $this->ejecucionFlujo;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'formulario' => $this->getFormularioVersion()->getNombre(),
            'fecha_hora' => $this->getFechaHora(),
            'resumen' => $this->getResumen(),
        ];
    }

    public function __sleep()
    {
        return array('id', 'fecha_hora', 'fecha_sticker', 'resumen', 'busqueda', 'formulario_version_id', 'estado_id', 'usuario_id', 'radicacion_year', 'radicacion_counter', 'formulario_version', 'fecha_formulario_version', 'nombre_formulario', 'nomenclatura_formulario', 'consecutivo', 'tipo_correspondencia', 'radicado', 'ejecucion_paso_id', 'ejecucion_flujo_id');
    }
}
