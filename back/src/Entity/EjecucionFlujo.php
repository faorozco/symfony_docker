<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\EjecucionFlujo\IniciarFlujo;
use App\Controller\EjecucionFlujo\ConsultarRadicado;
use App\Controller\EjecucionFlujo\ConsultarPorUsuario;
use App\Controller\EjecucionFlujo\ConsultarPorId;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "iniciar"={ 
 *          "method"="POST",
 *          "path"="/ejecucion_flujos/{id}/iniciar",
 *          "controller"=IniciarFlujo::class
 *       },
 *      "radicado"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_flujos/{radicado}/radicado",
 *          "controller"=ConsultarRadicado::class
 *       },
 *      "usuario"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_flujos/usuario",
 *          "controller"=ConsultarPorUsuario::class
 *       },
 *      "ejecucion_flujo"={ 
 *          "method"="GET",
 *          "path"="/ejecucion_flujos/{id}/ejecucion_flujo_trabajo",
 *          "controller"=ConsultarPorId::class
 *       },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/ejecucion_flujos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 * App\Entity\EjecucionFlujo
 *
 * @ORM\Entity(repositoryClass="App\Repository\EjecucionFlujoRepository")
 * @ORM\Table(name="ejecucion_flujo", indexes={@ORM\Index(name="fk_ejecucion_flujo_flujo1_idx", columns={"flujo_trabajo_version_id"}), @ORM\Index(name="fk_ejecucion_flujo_usuario_idx", columns={"usuario_id"})})
 */
class EjecucionFlujo
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
    protected $flujo_trabajo_version_id;

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
    protected $usuario_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $radicado;

    /**
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    protected $estado;

    /**
     * @ORM\ManyToOne(targetEntity="FlujoTrabajoVersion")
     * @ORM\JoinColumn(name="flujo_trabajo_version_id", referencedColumnName="id", nullable=false)
     */
    protected $flujo_trabajo_version;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     */
    protected $usuario;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\EjecucionFlujo
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
     * Set the value of flujo_trabajo_version_id.
     *
     * @param integer $flujo_trabajo_version_id
     * @return \App\Entity\EjecucionFlujo
     */
    public function setFlujoTrabajoVersionId($flujo_trabajo_version_id)
    {
        $this->flujo_trabajo_version_id = $flujo_trabajo_version_id;

        return $this;
    }

    /**
     * Get the value of flujo_trabajo_version_id.
     *
     * @return integer
     */
    public function getFlujoTrabajoVersionId()
    {
        return $this->flujo_trabajo_version_id;
    }

    /**
     * Set the value of fecha_inicio.
     *
     * @param \DateTime $fecha_inicio
     * @return \App\Entity\EjecucionFlujo
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
     * @return \App\Entity\EjecucionFlujo
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
     * @return \App\Entity\EjecucionFlujo
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
     * Set the value of usuario_id.
     *
     * @param integer $usuario_id
     * @return \App\Entity\EjecucionFlujo
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
     * Set the value of radicado.
     *
     * @param string $estado
     * @return \App\Entity\EjecucionFlujo
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
     * Set the value of estado.
     *
     * @param string $estado
     * @return \App\Entity\EjecucionFlujo
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
     * Set the value of flujo_trabajo_version.
     *
     * @param \App\Entity\FlujoTrabajoVersion $flujo_trabajo_version
     * @return \App\Entity\EjecucionFlujo
     */
    public function setFlujoTrabajoVersion(FlujoTrabajoVersion $flujo_trabajo_version)
    {
        $this->flujo_trabajo_version = $flujo_trabajo_version;

        return $this;
    }

    /**
     * Get the value of flujo_trabajo_version.
     *
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function getFlujoTrabajoVersion()
    {
        return $this->flujo_trabajo_version;
    }

    /**
     * Get the value of usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set the value of usuario
     *
     * @return  self
     */
    public function setUsuario(Usuario $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function __sleep()
    {
        return array('id', 'flujo_trabajo_version_id', 'fecha_inicio', 'fecha_vencimiento', 'fecha_fin', 'usuario_id', 'estado', 'radicado');
    }
}
