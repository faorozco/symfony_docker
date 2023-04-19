<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use App\Controller\FlujoTrabajo\FlujoTrabajoCreate;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\DuplicateWorkflow;
use App\Controller\ActivateWorkFlow;
use App\Controller\Paso\PasosByFlujo;
use App\Controller\InactivateWorkFlow;
use App\Controller\FlujoTrabajo\FlujoTrabajoVersionar;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "create"={
*          "method"="POST",
 *          "path"="/flujo_trabajos",
 *          "controller"=FlujoTrabajoCreate::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/flujo_trabajos/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "duplicate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos/{id}/duplicate",
 *          "controller"=DuplicateWorkflow::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "inactivate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos/{id}/inactivate",
 *          "controller"=InactivateWorkFlow::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "activate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos/{id}/activate",
 *          "controller"=ActivateWorkFlow::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "generateversion"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos/{id}/generateversion",
 *          "controller"=FlujoTrabajoVersionar::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "getpasosflujo"={
 *          "method"="GET",
 *          "path"="/flujo_trabajos/{id}/pasos",
 *          "controller"=PasosByFlujo::class
 *      }
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre"},
 *      arguments={"orderParameterName"="order"}) 
 * 
 * App\Entity\FlujoTrabajo
 *
 * @ORM\Entity()
 * @ORM\Table(name="flujo_trabajo")
 */
class FlujoTrabajo
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
    protected $estado_id;


    /**
     * @ORM\OneToMany(targetEntity="Paso", mappedBy="flujoTrabajo")
     * @ORM\JoinColumn(name="id", referencedColumnName="flujo_trabajo_id", nullable=false)
     */
    protected $pasos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $formulario_id;

    /**
     * @ORM\ManyToOne(targetEntity="Formulario", inversedBy="flujoTrabajos")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=false)
     */
    protected $formulario;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Groups({"nombre"})
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity="FlujoTrabajoVersion", mappedBy="flujoTrabajo")
     */
    protected $listFlujoTrabajoVersion;

    public function __construct()
    {
        $this->pasos = new ArrayCollection();
        $this->listFlujoTrabajoVersion = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\FlujoTrabajo
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
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\Plantilla
     */
    public function setFormularioId($formulario_id)
    {
        $this->formulario_id = $formulario_id;

        return $this;
    }

    /**
     * Get the value of formulario_id.
     *
     * @return integer
     */
    public function getFormularioId()
    {
        return $this->formulario_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\FlujoTrabajo
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
     * Add Paso entity to collection (one to many).
     *
     * @param \App\Entity\Paso $paso
     * @return \App\Entity\FlujoTrabajo
     */
    public function addPaso(Paso $paso)
    {
        $this->pasos[] = $paso;

        return $this;
    }

    /**
     * Remove Paso entity from collection (one to many).
     *
     * @param \App\Entity\Paso $paso
     * @return \App\Entity\FlujoTrabajo
     */
    public function removePaso(Paso $paso)
    {
        $this->pasos->removeElement($paso);

        return $this;
    }

    /**
     * Get Paso entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPasos()
    {
        return $this->pasos;
    }

    /**
     * Set Formulario entity (manny to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\FlujoTrabajo
     */
    public function setFormulario(Formulario $formulario)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Get Formulario entity (many to one).
     *
     * @return \App\Entity\Formulario
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    /**
     * Get the value of nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\FlujoTrabajo
     */
    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function __sleep()
    {
        return array('id', 'estado_id', 'nombre', 'descripcion', 'version');
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Add FlujoTrabajoVersion entity to collection (one to many).
     *
     * @param \App\Entity\FlujoTrabajoVersion $FlujoTrabajoVersion
     * @return \App\Entity\FlujoTrabajo
     */
    public function addFlujoTrabajoVersion(FlujoTrabajoVersion $flujoTrabajoVersion)
    {
        $this->listFlujoTrabajoVersion[] = $flujoTrabajoVersion;

        return $this;
    }

    /**
     * Remove FlujoTrabajoVersion entity from collection (one to many).
     *
     * @param \App\Entity\FlujoTrabajoVersion $FlujoTrabajoVersion
     * @return \App\Entity\FlujoTrabajo
     */
    public function removeFlujoTrabajoVersion(FlujoTrabajoVersion $flujoTrabajoVersion)
    {
        $this->listFlujoTrabajoVersion->removeElement($flujoTrabajoVersion);

        return $this;
    }

    /**
     * Get FlujoTrabajoVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListFlujoTrabajoVersion()
    {
        return $this->listFlujoTrabajoVersion;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'descripcion' => $this->getDescripcion(),
            'estado_id' => $this->getEstadoId(),
            'version' => $this->getVersion(),
        ];
    }
}
