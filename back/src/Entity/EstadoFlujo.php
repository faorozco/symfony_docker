<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/estado_flujos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 * App\Entity\EstadoFlujo
 *
 * @ORM\Entity()
 * @ORM\Table(name="estado_flujo", indexes={@ORM\Index(name="fk_estado_flujo_proceso1_idx", columns={"proceso_id"}), @ORM\Index(name="fk_estado_flujo_creador_idx", columns={"creador_id"}), @ORM\Index(name="fk_estado_flujo_paso1_idx", columns={"ultimo_paso"}), @ORM\Index(name="fk_estado_flujo_ultimo_responsable_idx", columns={"ultimo_responsable_id"}), @ORM\Index(name="fk_estado_flujo_responsable_paso_idx", columns={"responsable_paso_id"})})
 */
class EstadoFlujo
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
    protected $proceso_id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $hora;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ultimo_paso;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $creador_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ultimo_responsable_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_vencimiento_paso;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $responsable_paso_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_vencimiento_flujo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="Proceso", inversedBy="estadoFlujos")
     * @ORM\JoinColumn(name="proceso_id", referencedColumnName="id", nullable=false)
     */
    protected $proceso;

    /**
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="creador")
     * @ORM\JoinColumn(name="creador_id", referencedColumnName="id")
     */
    protected $creador;

    /**
     * @ORM\ManyToOne(targetEntity="Paso", inversedBy="estadoFlujos")
     * @ORM\JoinColumn(name="ultimo_paso", referencedColumnName="id", nullable=false)
     */
    protected $paso;

    /**
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="ultimoResponsable")
     * @ORM\JoinColumn(name="ultimo_responsable_id", referencedColumnName="id")
     */
    protected $ultimoResponsable;

    /**
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="responsablePaso")
     * @ORM\JoinColumn(name="responsable_paso_id", referencedColumnName="id")
     */
    protected $responsablePaso;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\EstadoFlujo
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
     * Set the value of proceso_id.
     *
     * @param integer $proceso_id
     * @return \App\Entity\EstadoFlujo
     */
    public function setProcesoId($proceso_id)
    {
        $this->proceso_id = $proceso_id;

        return $this;
    }

    /**
     * Get the value of proceso_id.
     *
     * @return integer
     */
    public function getProcesoId()
    {
        return $this->proceso_id;
    }

    /**
     * Set the value of fecha.
     *
     * @param \DateTime $fecha
     * @return \App\Entity\EstadoFlujo
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get the value of fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set the value of hora.
     *
     * @param \DateTime $hora
     * @return \App\Entity\EstadoFlujo
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get the value of hora.
     *
     * @return \DateTime
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set the value of ultimo_paso.
     *
     * @param integer $ultimo_paso
     * @return \App\Entity\EstadoFlujo
     */
    public function setUltimoPaso($ultimo_paso)
    {
        $this->ultimo_paso = $ultimo_paso;

        return $this;
    }

    /**
     * Get the value of ultimo_paso.
     *
     * @return integer
     */
    public function getUltimoPaso()
    {
        return $this->ultimo_paso;
    }

    /**
     * Set the value of creador_id.
     *
     * @param integer $creador_id
     * @return \App\Entity\EstadoFlujo
     */
    public function setCreadorId($creador_id)
    {
        $this->creador_id = $creador_id;

        return $this;
    }

    /**
     * Get the value of creador_id.
     *
     * @return integer
     */
    public function getCreadorId()
    {
        return $this->creador_id;
    }

    /**
     * Set the value of ultimo_responsable_id.
     *
     * @param integer $ultimo_responsable_id
     * @return \App\Entity\EstadoFlujo
     */
    public function setUltimoResponsableId($ultimo_responsable_id)
    {
        $this->ultimo_responsable_id = $ultimo_responsable_id;

        return $this;
    }

    /**
     * Get the value of ultimo_responsable_id.
     *
     * @return integer
     */
    public function getUltimoResponsableId()
    {
        return $this->ultimo_responsable_id;
    }

    /**
     * Set the value of fecha_vencimiento_paso.
     *
     * @param \DateTime $fecha_vencimiento_paso
     * @return \App\Entity\EstadoFlujo
     */
    public function setFechaVencimientoPaso($fecha_vencimiento_paso)
    {
        $this->fecha_vencimiento_paso = $fecha_vencimiento_paso;

        return $this;
    }

    /**
     * Get the value of fecha_vencimiento_paso.
     *
     * @return \DateTime
     */
    public function getFechaVencimientoPaso()
    {
        return $this->fecha_vencimiento_paso;
    }

    /**
     * Set the value of responsable_paso_id.
     *
     * @param integer $responsable_paso_id
     * @return \App\Entity\EstadoFlujo
     */
    public function setResponsablePasoId($responsable_paso_id)
    {
        $this->responsable_paso_id = $responsable_paso_id;

        return $this;
    }

    /**
     * Get the value of responsable_paso_id.
     *
     * @return integer
     */
    public function getResponsablePasoId()
    {
        return $this->responsable_paso_id;
    }

    /**
     * Set the value of fecha_vencimiento_flujo.
     *
     * @param \DateTime $fecha_vencimiento_flujo
     * @return \App\Entity\EstadoFlujo
     */
    public function setFechaVencimientoFlujo($fecha_vencimiento_flujo)
    {
        $this->fecha_vencimiento_flujo = $fecha_vencimiento_flujo;

        return $this;
    }

    /**
     * Get the value of fecha_vencimiento_flujo.
     *
     * @return \DateTime
     */
    public function getFechaVencimientoFlujo()
    {
        return $this->fecha_vencimiento_flujo;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\EstadoFlujo
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
     * Set Proceso entity (many to one).
     *
     * @param \App\Entity\Proceso $proceso
     * @return \App\Entity\EstadoFlujo
     */
    public function setProceso(Proceso $proceso = null)
    {
        $this->proceso = $proceso;

        return $this;
    }

    /**
     * Get Proceso entity (many to one).
     *
     * @return \App\Entity\Proceso
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     * Get the value of creador
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * Set the value of creador
     *
     * @return  self
     */
    public function setCreador(Usuario $creador)
    {
        $this->creador = $creador;

        return $this;
    }

    /**
     * Get the value of ultimo_responsable
     */
    public function getUltimoResponsable()
    {
        return $this->ultimo_responsable;
    }

    /**
     * Set the value of ultimo_responsable
     *
     * @return  self
     */
    public function setUltimoResponsable(Usuario $ultimo_responsable)
    {
        $this->ultimo_responsable = $ultimo_responsable;

        return $this;
    }

    /**
     * Get the value of responsable_paso
     */
    public function getResponsablePaso()
    {
        return $this->responsable_paso;
    }

    /**
     * Set the value of responsable_paso
     *
     * @return  self
     */
    public function setResponsablePaso(Usuario $responsable_paso)
    {
        $this->responsable_paso = $responsable_paso;

        return $this;
    }

    /**
     * Set Paso entity (many to one).
     *
     * @param \App\Entity\Paso $paso
     * @return \App\Entity\EstadoFlujo
     */
    public function setPaso(Paso $paso = null)
    {
        $this->paso = $paso;

        return $this;
    }

    /**
     * Get Paso entity (many to one).
     *
     * @return \App\Entity\Paso
     */
    public function getPaso()
    {
        return $this->paso;
    }

    public function __sleep()
    {
        return array('id', 'proceso_id', 'fecha', 'hora', 'creador_id', 'ultimo_paso', 'ultimo_responsable_id', 'fecha_vencimiento_paso', 'responsable_paso_id', 'fecha_vencimiento_flujo', 'estado_id');
    }
}
