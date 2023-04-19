<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\DuplicateWorkflow;
use App\Controller\ActivateWorkFlow;
use App\Controller\FlujoTrabajoVersion\FlujoTrabajoVersionAssociate;
use App\Controller\Paso\PasosByFlujo;
use App\Controller\InactivateWorkFlow;
use App\Controller\FlujoTrabajo\FlujoTrabajoVersionar;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "create"={
*          "method"="POST",
 *         "path"="/flujo_trabajos_version",
*          "requirements"={
 *              "estadoId"="{ 0 , 1 }",
 *              "nombre"="\d+",
 *              "descripcion"="\d+",
 *              "version"="/^\d+$/"
 *          }
 *      },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/flujo_trabajos_version/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "duplicate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos_version/{id}/duplicate",
 *          "controller"=DuplicateWorkflow::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "inactivate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos_version/{id}/inactivate",
 *          "controller"=InactivateWorkFlow::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "activate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos_version/{id}/activate",
 *          "controller"=ActivateWorkFlow::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "associate"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos_version/{id}/associate",
 *          "controller"=FlujoTrabajoVersionAssociate::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "generateversion"={
 *          "method"="POST",
 *          "path"="/flujo_trabajos_version/{id}/generateversion",
 *          "controller"=FlujoTrabajoVersionar::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "getpasosflujo"={
 *          "method"="GET",
 *          "path"="/flujo_trabajos_version/{id}/pasos",
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
 * App\Entity\FlujoTrabajoVersion
 *
 * @ORM\Entity(repositoryClass="App\Repository\FlujoTrabajoVersionRepository")
 * @ORM\Table(name="flujo_trabajo_version")
 */
class FlujoTrabajoVersion
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
     * @ORM\OneToMany(targetEntity="PasoVersion", mappedBy="flujoTrabajoVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="flujo_trabajo_version_id", nullable=false)
     */
    protected $pasosVersion;

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
     * @ORM\ManyToOne(targetEntity="FlujoTrabajo", inversedBy="listFlujoTrabajoVersion")
     * @ORM\JoinColumn(name="flujo_trabajo_id", referencedColumnName="id", nullable=true)
     */
    protected $flujoTrabajo;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $flujo_trabajo_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $formulario_version_id;

    /**
     * @ORM\ManyToOne(targetEntity="FormularioVersion")
     * @ORM\JoinColumn(name="formulario_version_id", referencedColumnName="id", nullable=true)
     */
    protected $formularioVersion;

    public function __construct()
    {
        $this->pasosVersion = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\FlujoTrabajoVersion
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\FlujoTrabajoVersion
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
     * Add PasoVersion entity to collection (one to many).
     *
     * @param \App\Entity\PasoVersion $pasoVersion
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function addPasoVersion(PasoVersion $pasoVersion)
    {
        $this->pasosVersion[] = $pasoVersion;

        return $this;
    }

    /**
     * Remove PasoVersion entity from collection (one to many).
     *
     * @param \App\Entity\PasoVersion $pasoVersion
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function removePasoVersion(PasoVersion $pasoVersion)
    {
        $this->pasosVersion->removeElement($pasoVersion);

        return $this;
    }

    /**
     * Get PasoVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPasosVersion()
    {
        return $this->pasosVersion;
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
     * @return \App\Entity\FlujoTrabajoVersion
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
     * Set the value of flujo_trabajo_id.
     *
     * @param integer $flujo_trabajo_id
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function setFlujoTrabajoId($flujo_trabajo_id)
    {
        $this->flujo_trabajo_id = $flujo_trabajo_id;

        return $this;
    }

    /**
     * Get the value of flujo_trabajo_id.
     *
     * @return integer
     */
    public function getFlujoTrabajoId()
    {
        return $this->flujo_trabajo_id;
    }

    /**
     * Set the value of flujo_trabajo.
     *
     * @param integer $flujo_trabajo
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function setFlujoTrabajo($flujoTrabajo)
    {
        $this->flujoTrabajo = $flujoTrabajo;

        return $this;
    }

    /**
     * Get the value of flujo_trabajo.
     *
     * @return \App\Entity\FlujoTrabajo
     */
    public function getFlujoTrabajo()
    {
        return $this->flujoTrabajo;
    }

    /**
     * Set the value of formulario_version_id.
     *
     * @param integer $formulario_version_id
     * @return \App\Entity\FlujoTrabajoVersion
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
     * Set FormularioVersion entity (one to one).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\FlujoTrabajoVersion
     */
    public function setFormularioVersion(FormularioVersion $formularioVersion)
    {
        $this->formularioVersion = $formularioVersion;

        return $this;
    }

    /**
     * Get FormularioVersion entity (one to one).
     *
     * @return \App\Entity\FormularioVersion
     */
    public function getFormularioVersion()
    {
        return $this->formularioVersion;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'descripcion' => $this->getDescripcion(),
            'estado_id' => $this->getEstadoId(),
            'flujo_trabajo_id' => $this->getFlujoTrabajoId(),
            'formulario_version_id' => $this->getFormularioVersionId(),
            'version' => $this->getVersion(),
        ];
    }
}
