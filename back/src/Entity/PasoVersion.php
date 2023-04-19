<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\PasoVersion\CargarAcciones;
use App\Controller\PasoVersion\CargarAccionesFlujoTrabajo;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "delete",
 *      "get"={
 *         "method"="GET",
 *         "path"="/pasos_version/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "cargarAcciones"={
 *          "method"="GET",
 *          "path"="/pasos_version/{id}/cargar_acciones",
 *          "controller"=CargarAcciones::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "cargarAccionesFlujoTrabajo"={
 *          "method"="GET",
 *          "path"="/pasos_version/{id}/cargar_acciones_flujo_trabajo",
 *          "controller"=CargarAccionesFlujoTrabajo::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *  }
 * )
 * App\Entity\PasoVersion
 *
 * @ORM\Entity()
 * @ORM\Table(name="paso_version", indexes={@ORM\Index(name="fk_paso_version_flujo_trabajo_version1_idx", columns={"flujo_trabajo_version_id"})})
 */
class PasoVersion
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
    protected $prioridad;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    protected $flujo_trabajo_version_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="PasoEventoVersion", mappedBy="Paso")
     * @ORM\JoinColumn(name="id", referencedColumnName="paso_id", nullable=false)
     */
    protected $eventosVersion;

    

    /**
     * @ORM\ManyToOne(targetEntity="FlujoTrabajoVersion", inversedBy="pasoVersion")
     * @ORM\JoinColumn(name="flujo_trabajo_version_id", referencedColumnName="id", nullable=false)
     */
    protected $flujoTrabajoVersion;

    /**
     * @ORM\Column(type="integer")
     */
    private $plazo;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $time;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="integer")
     */
    protected $paso_id;

    public function __construct()
    {
        $this->eventosVersion = new ArrayCollection();
    }



    /**
     * Add PasoEventoVersion entity to collection (one to many).
     *
     * @param \App\Entity\PasoEventoVersion $eventoVersion
     * @return \App\Entity\PasoVersion
     */
    public function addPasoEventoVersion(PasoEventoVersion $eventoVersion)
    {
        $this->eventosVersion[] = $eventoVersion;

        return $this;
    }

    /**
     * Remove PasoEventoVersion entity from collection (one to many).
     *
     * @param \App\Entity\PasoEventoVersion $eventoVersion
     * @return \App\Entity\PasoVersion
     */
    public function removePasoEventoVersion(PasoEventoVersion $eventoVersion)
    {
        $this->eventosVersion->removeElement($eventoVersion);

        return $this;
    }

    /**
     * Get PasoEventoVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPasoEventoVersion()
    {
        return $this->eventosVersion;
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\PasoVersion
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
     * Set the value of id.
     *
     * @param integer $plazo
     * @return \App\Entity\PasoVersion
     */
    public function setPlazo($plazo)
    {
        $this->plazo = $plazo;

        return $this;
    }

    /**
     * Get the value of plazo.
     *
     * @return integer
     */
    public function getPlazo()
    {
        return $this->plazo;
    }

    /**
     * Set the value of prioridad.
     *
     * @param integer $prioridad
     * @return \App\Entity\PasoVersion
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get the value of prioridad.
     *
     * @return integer
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\PasoVersion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of flujo_trabajo_version_id.
     *
     * @param integer $flujo_trabajo_version_id
     * @return \App\Entity\PasoVersion
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\PasoVersion
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
     * Set FlujoTrabajoVersion entity (many to one).
     *
     * @param \App\Entity\FlujoTrabajoVersion $flujoTrabajoVersion
     * @return \App\Entity\PasoVersion
     */
    public function setFlujoTrabajoVersion(FlujoTrabajoVersion $flujoTrabajoVersion = null)
    {
        $this->flujoTrabajoVersion = $flujoTrabajoVersion;

        return $this;
    }

    /**
     * Get FlujoTrabajoVersion entity (many to one).
     *
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function getFlujoTrabajoVersion()
    {
        return $this->flujoTrabajoVersion;
    }

    public function __sleep()
    {
        return array('id', 'prioridad', 'descripcion', 'flujo_trabajo_version_id', 'estado_id','plazo','time');
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Set the value of paso_id.
     *
     * @param integer $id
     * @return \App\Entity\PasoVersion
     */
    public function setPasoId($paso_id)
    {
        $this->paso_id = $paso_id;

        return $this;
    }

    /**
     * Get the value of paso_id.
     *
     * @return integer
     */
    public function getPasoId()
    {
        return $this->paso_id;
    }


    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'prioridad' => $this->getPrioridad(),
            'descripcion' => $this->getDescripcion(),
            'plazo' => $this->getPlazo(),
            'time' => $this->getTime(),
            'numero' => $this->getNumero()
        ];
    }
}
