<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\EjecucionPaso\CompletarPaso;
use App\Controller\EjecucionPaso\SiguientePaso;
use App\Controller\EjecucionPaso\GetFormFlujo;
use App\Controller\EjecucionPaso\CambiarResponsable;
use App\Controller\EjecucionPaso\AsignarResponsableVistoBueno;
use App\Controller\EjecucionPaso\AprobarVistoBueno;
use App\Controller\EjecucionPaso\AplazarFecha;
use App\Controller\EjecucionPaso\PasoRemitente;
use App\Controller\EjecucionPaso\SummaryStep;
use App\Controller\EjecucionPaso\TotalSteps;
use App\Controller\EjecucionPaso\DevolucionStep;


/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "completar"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/completar",
 *          "controller"=CompletarPaso::class
 *       },
 *      "siguientePaso"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/siguiente_paso",
 *          "controller"=SiguientePaso::class
 *       },
 *      "cambiarResponsable"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/cambiar_responsable",
 *          "controller"=CambiarResponsable::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "getFormPasosFlujo"={ 
 *          "method"="POST",
 *          "path"="/ejecucion_pasos/formrelation",
 *          "controller"=GetFormFlujo::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "asignarResponsableVistoBueno"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/asignar_responsable_visto_bueno",
 *          "controller"=AsignarResponsableVistoBueno::class,
 *          "defaults"={"_api_receive"=false}
 *       },
*      "devolucionStep"={ 
 *          "method"="POST",
 *          "path"="/ejecucion_pasos/{id}/returnStep",
 *          "controller"=DevolucionStep::class,
 *          "defaults"={"_api_receive"=false}
 *       }, 
 *       "vistoBueno"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/visto_bueno",
 *          "controller"=AprobarVistoBueno::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "aplazarFecha"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/aplazar_tarea",
 *          "controller"=AplazarFecha::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *       "pasoRemitente"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{id}/paso_remitente",
 *          "controller"=PasoRemitente::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *        "summaryStep"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{ejecucionFlujoId}/summary_step",
 *          "controller"=SummaryStep::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *       "totalSteps"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_pasos/{ejecucionFlujoId}/total_steps",
 *          "controller"=TotalSteps::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/ejecucion_pasos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 * App\Entity\EjecucionPaso
 *
 * @ORM\Entity(repositoryClass="App\Repository\EjecucionPasoRepository")
 * @ORM\Table(name="ejecucion_paso", indexes={@ORM\Index(name="fk_ejecucion_paso_paso1_idx", columns={"paso_version_id"})})
 */
class EjecucionPaso
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $paso_version_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ejecucion_flujo_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ejecucion_flujo_id_iniciado;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_inicio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_vencimiento;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_fin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $grupo_responsable_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $usuario_responsable_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $usuario_remitente_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $usuario_responsable_visto_bueno_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ejecucion_paso_id_siguiente;

    /**
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    protected $estado;

    /**
     * @ORM\ManyToOne(targetEntity="PasoVersion")
     * @ORM\JoinColumn(name="paso_version_id", referencedColumnName="id", nullable=false)
     */
    protected $pasoVersion;

    /**
     * @ORM\ManyToOne(targetEntity="EjecucionFlujo")
     * @ORM\JoinColumn(name="ejecucion_flujo_id", referencedColumnName="id", nullable=false)
     */
    protected $ejecucionFlujo;

    /**
     * @ORM\ManyToOne(targetEntity="EjecucionFlujo")
     * @ORM\JoinColumn(name="ejecucion_flujo_id_iniciado", referencedColumnName="id", nullable=true)
     */
    protected $ejecucionFlujoIniciado;

    /**
     * @ORM\ManyToOne(targetEntity="Grupo")
     * @ORM\JoinColumn(name="grupo_id", referencedColumnName="id", nullable=true)
     */
    protected $grupoResponsable;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="usuario_responsable_id", referencedColumnName="id", nullable=true)
     */
    protected $usuarioResponsable;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="usuario_remitente_id", referencedColumnName="id", nullable=true)
     */
    protected $usuarioRemitente;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="usuario_responsable_visto_bueno_id", referencedColumnName="id", nullable=true)
     */
    protected $usuarioResponsableVistoBueno;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $fill_form;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $file;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $visto_bueno;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tempProperties = [];

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $devolucion;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\EjecucionPaso
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
     * Set the value of paso_version_id.
     *
     * @param integer $paso_version_id
     * @return \App\Entity\EjecucionPaso
     */
    public function setPasoVersionId($paso_version_id)
    {
        $this->paso_version_id = $paso_version_id;

        return $this;
    }

    /**
     * Get the value of paso_version_id.
     *
     * @return integer
     */
    public function getPasoVersionId()
    {
        return $this->paso_version_id;
    }

    /**
     * Set the value of ejecucion_flujo_id.
     *
     * @param integer $ejecucion_flujo_id
     * @return \App\Entity\EjecucionPaso
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
     * Set the value of ejecucion_flujo_id_iniciado.
     *
     * @param integer $ejecucion_flujo_id_iniciado
     * @return \App\Entity\EjecucionPaso
     */
    public function setEjecucionFlujoIdIniciado($ejecucion_flujo_id_iniciado)
    {
        $this->ejecucion_flujo_id_iniciado = $ejecucion_flujo_id_iniciado;

        return $this;
    }

    /**
     * Get the value of ejecucion_flujo_id_iniciado.
     *
     * @return integer
     */
    public function getEjecucionFlujoIdIniciado()
    {
        return $this->ejecucion_flujo_id_iniciado;
    }

    /**
     * Set the value of fecha_inicio.
     *
     * @param \DateTime $fecha_inicio
     * @return \App\Entity\EjecucionPaso
     */
    public function setFechaInicio($fecha_inicio)
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    /**
     * Get the value of fecha_inicio.
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fecha_inicio;
    }

    /**
     * Set the value of fecha_vencimiento.
     *
     * @param \DateTime $fecha_vencimiento
     * @return \App\Entity\EjecucionPaso
     */
    public function setFechaVencimiento($fecha_vencimiento)
    {
        $this->fecha_vencimiento = $fecha_vencimiento;

        return $this;
    }

    /**
     * Get the value of fecha_vencimiento.
     *
     * @return \DateTime
     */
    public function getFechaVencimiento()
    {
        return $this->fecha_vencimiento;
    }

    /**
     * Set the value of fecha_fin.
     *
     * @param \DateTime $fecha_fin
     * @return \App\Entity\EjecucionPaso
     */
    public function setFechaFin($fecha_fin)
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }

    /**
     * Get the value of fecha_fin.
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fecha_fin;
    }

    /**
     * Set the value of grupo_responsable_id.
     *
     * @param integer  grupo_responsable_id
     * @return \App\Entity\EjecucionPaso
     */
    public function setGrupoResponsableId($grupo_responsable_id)
    {
        $this->grupo_responsable_id = $grupo_responsable_id;

        return $this;
    }

    /**
     * Get the value of grupo_responsable_id.
     *
     * @return integer
     */
    public function getGrupoResponsableId()
    {
        return $this->grupo_responsable_id;
    }

    /**
     * Set the value of usuario_responsable_id.
     *
     * @param integer $usuario_responsable_id
     * @return \App\Entity\EjecucionPaso
     */
    public function setUsuarioResponsableId($usuario_responsable_id)
    {
        $this->usuario_responsable_id = $usuario_responsable_id;

        return $this;
    }

    /**
     * Get the value of usuario_responsable_id.
     *
     * @return integer
     */
    public function getUsuarioResponsableId()
    {
        return $this->usuario_responsable_id;
    }

    /**
     * Set the value of usuario_remitente_id.
     *
     * @param integer $usuario_remitente_id
     * @return \App\Entity\EjecucionPaso
     */
    public function setUsuarioRemitenteId($usuario_remitente_id)
    {
        $this->usuario_remitente_id = $usuario_remitente_id;

        return $this;
    }

    /**
     * Get the value of usuario_remitente_id.
     *
     * @return integer
     */
    public function getUsuarioRemitenteId()
    {
        return $this->usuario_remitente_id;
    }

    /**
     * Set the value of usuario_responsable_visto_bueno_id.
     *
     * @param integer $usuario_responsable_visto_bueno_id
     * @return \App\Entity\EjecucionPaso
     */
    public function setUsuarioResponsableVistoBuenoId($usuario_responsable_visto_bueno_id)
    {
        $this->usuario_responsable_visto_bueno_id = $usuario_responsable_visto_bueno_id;

        return $this;
    }

    /**
     * Get the value of usuario_responsable_visto_bueno_id.
     *
     * @return integer
     */
    public function getUsuarioResponsableVistoBuenoId()
    {
        return $this->usuario_responsable_visto_bueno_id;
    }

    /**
     * Set the value of ejecucion_paso_id_siguiente.
     *
     * @param integer $ejecucion_paso_id_siguiente
     * @return \App\Entity\EjecucionPaso
     */
    public function setEjecucionPasoIdSiguiente($ejecucion_paso_id_siguiente)
    {
        $this->ejecucion_paso_id_siguiente = $ejecucion_paso_id_siguiente;

        return $this;
    }

    /**
     * Get the value of ejecucion_paso_id_siguiente.
     *
     * @return integer
     */
    public function getEjecucionPasoIdSiguiente()
    {
        return $this->ejecucion_paso_id_siguiente;
    }

    /**
     * Set the value of estado.
     *
     * @param string $estado
     * @return \App\Entity\EjecucionPaso
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of estado.
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of pasoVersion.
     *
     * @param \App\Entity\PasoVersion $pasoVersion
     * @return \App\Entity\EjecucionPaso
     */
    public function setPasoVersion(PasoVersion $pasoVersion)
    {
        $this->pasoVersion = $pasoVersion;

        return $this;
    }

    /**
     * Get the value of pasoVersion.
     *
     * @return \App\Entity\PasoVersion
     */
    public function getPasoVersion()
    {
        return $this->pasoVersion;
    }

    /**
     * Set the value of ejecucionFlujo.
     *
     * @param \App\Entity\EjecucionFlujo $ejecucionFlujo
     * @return \App\Entity\EjecucionPaso
     */
    public function setEjecucionFlujo(EjecucionFlujo $ejecucionFlujo)
    {
        $this->ejecucionFlujo = $ejecucionFlujo;

        return $this;
    }

    /**
     * Get the value of ejecucionFlujo.
     *
     * @return \App\Entity\EjecucionFlujo
     */
    public function getEjecucionFlujo()
    {
        return $this->ejecucionFlujo;
    }

    /**
     * Set the value of ejecucionFlujoIniciado.
     *
     * @param \App\Entity\EjecucionFlujo $ejecucionFlujo
     * @return \App\Entity\EjecucionPaso
     */
    public function setEjecucionFlujoIniciado(EjecucionFlujo $ejecucionFlujoIniciado)
    {
        $this->ejecucionFlujoIniciado = $ejecucionFlujoIniciado;

        return $this;
    }

    /**
     * Get the value of ejecucionFlujoIniciado.
     *
     * @return \App\Entity\EjecucionFlujo
     */
    public function getEjecucionFlujoIniciado()
    {
        return $this->ejecucionFlujoIniciado;
    }

    /**
     * Set the value of grupoResponsable.
     *
     * @param \App\Entity\Grupo $grupoResponsable
     * @return \App\Entity\EjecucionPaso
     */
    public function setGrupoResponsable(Grupo $grupoResponsable)
    {
        $this->grupoResponsable = $grupoResponsable;

        return $this;
    }

    /**
     * Get the value of grupoResponsable.
     *
     * @return \App\Entity\Grupo
     */
    public function getGrupo()
    {
        return $this->grupoResponsable;
    }

    /**
     * Set the value of usuarioResponsable.
     *
     * @param \App\Entity\Usuario $usuarioResponsable
     * @return \App\Entity\EjecucionPaso
     */
    public function setUsuarioResponsable(Usuario $usuarioResponsable)
    {
        $this->usuarioResponsable = $usuarioResponsable;

        return $this;
    }

    /**
     * Get the value of usuarioResponsable.
     *
     * @return \App\Entity\Usuario
     */
    public function getUsuarioResponsable()
    {
        return $this->usuarioResponsable;
    }

    /**
     * Set the value of usuarioRemitente.
     *
     * @param \App\Entity\Usuario $usuarioRemitente
     * @return \App\Entity\EjecucionPaso
     */
    public function setUsuarioRemitente(Usuario $usuarioRemitente)
    {
        $this->usuarioRemitente = $usuarioRemitente;

        return $this;
    }

    /**
     * Get the value of usuarioRemitente.
     *
     * @return \App\Entity\Usuario
     */
    public function getUsuarioRemitente()
    {
        return $this->usuarioRemitente;
    }

    /**
     * Set the value of usuarioResponsableVistoBueno.
     *
     * @param \App\Entity\Usuario $usuarioResponsableVistoBueno
     * @return \App\Entity\EjecucionPaso
     */
    public function setUsuarioResponsableVistoBueno(Usuario $usuarioResponsableVistoBueno)
    {
        $this->usuarioResponsableVistoBueno = $usuarioResponsableVistoBueno;

        return $this;
    }

    /**
     * Get the value of usuarioResponsableVistoBueno.
     *
     * @return \App\Entity\Usuario
     */
    public function getUsuarioResponsableVistoBueno()
    {
        return $this->usuarioResponsableVistoBueno;
    }

    /**
     * Set the value of fill_form.
     *
     * @param boolean $fill_form
     * @return \App\Entity\EjecucionFlujo
     */
    public function setFillForm($fill_form)
    {
        $this->fill_form = $fill_form;

        return $this;
    }

    /**
     * Get the value of fill_form.
     *
     * @return boolean
     */
    public function getFillForm()
    {
        return $this->fill_form;
    }

    public function __sleep()
    {
        return array('id', 'paso_version_id', 'fecha_inicio', 'fecha_vencimiento', 'fecha_fin', 'grupo_responsable_id', 'usuario_responsable_id', 'usuario_remitente_id', 'estado', 'fill_form');
    }

    public function getComment(): ?bool
    {
        return $this->comment;
    }

    public function setComment(?bool $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getFile(): ?bool
    {
        return $this->file;
    }

    public function setFile(?bool $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getVistoBueno(): ?bool
    {
        return $this->visto_bueno;
    }

    public function setVistoBueno(?bool $visto_bueno): self
    {
        $this->visto_bueno = $visto_bueno;

        return $this;
    }

    /**
     * Set the value of tempProperties.
     *
     * @param self $tempProperties
     * @return \App\Entity\PasoEventoVersion
     */
    public function setTempProperties($tempProperties): self
    {
        $this->tempProperties = $tempProperties;

        return $this;
    }

    /**
     * Get the value of tempProperties.
     *
     * @return self
     */
    public function getTempProperties()
    {
        return $this->tempProperties;
    }

    public function getDevolucion(): ?string
    {
        return $this->devolucion;
    }

    public function setDevolucion(?string $devolucion): self
    {
        $this->devolucion = $devolucion;

        return $this;
    }
}
