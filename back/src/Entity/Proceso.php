<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\ProcesoExport;
use App\Controller\ProcesoImport;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *     "import"={
 *         "method"="POST",
 *         "path"="/procesos/import",
 *         "controller"=ProcesoImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *     "export"={
 *          "method"="POST",
 *          "path"="/procesos/export",
 *          "controller"=ProcesoExport::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/procesos/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre", "codigo_interno", "direccion_oficina", "extension", "telefono_fijo_oficina"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre", "codigo_interno", "direccion_oficina", "extension", "telefono_fijo_oficina"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\Proceso
 *
 * @ORM\Entity()
 * @ORM\Table(name="proceso", indexes={@ORM\Index(name="fk_proceso_sede1_idx", columns={"sede_id"})})
 */
class Proceso
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $codigo_interno;

    /**
     * @ORM\Column(type="string", length=400, nullable=true)
     */
    protected $direccion_oficina;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected $extension;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected $telefono_fijo_oficina;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sede_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="EstadoFlujo", mappedBy="proceso")
     * @ORM\JoinColumn(name="id", referencedColumnName="proceso_id", nullable=false)
     */
    protected $estadoFlujos;

    /**
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="proceso")
     * @ORM\JoinColumn(name="id", referencedColumnName="proceso_id", nullable=false)
     */
    protected $usuarios;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="procesos")
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id", nullable=false)
     */
    protected $sede;

    public function __construct()
    {
        $this->estadoFlujos = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Proceso
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
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\Proceso
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
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
     * Set the value of codigo_interno.
     *
     * @param string $codigo_interno
     * @return \App\Entity\Proceso
     */
    public function setCodigoInterno($codigo_interno)
    {
        $this->codigo_interno = $codigo_interno;

        return $this;
    }

    /**
     * Get the value of codigo_interno.
     *
     * @return string
     */
    public function getCodigoInterno()
    {
        return $this->codigo_interno;
    }

    /**
     * Set the value of direccion_oficina.
     *
     * @param string $direccion_oficina
     * @return \App\Entity\Proceso
     */
    public function setDireccionOficina($direccion_oficina)
    {
        $this->direccion_oficina = $direccion_oficina;

        return $this;
    }

    /**
     * Get the value of direccion_oficina.
     *
     * @return string
     */
    public function getDireccionOficina()
    {
        return $this->direccion_oficina;
    }

    /**
     * Set the value of extension.
     *
     * @param string $extension
     * @return \App\Entity\Proceso
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the value of extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the value of telefono_fijo_oficina.
     *
     * @param string $telefono_fijo_oficina
     * @return \App\Entity\Proceso
     */
    public function setTelefonoFijoOficina($telefono_fijo_oficina)
    {
        $this->telefono_fijo_oficina = $telefono_fijo_oficina;

        return $this;
    }

    /**
     * Get the value of telefono_fijo_oficina.
     *
     * @return string
     */
    public function getTelefonoFijoOficina()
    {
        return $this->telefono_fijo_oficina;
    }

    /**
     * Set the value of sede_id.
     *
     * @param integer $sede_id
     * @return \App\Entity\Proceso
     */
    public function setSedeId($sede_id)
    {
        $this->sede_id = $sede_id;

        return $this;
    }

    /**
     * Get the value of sede_id.
     *
     * @return integer
     */
    public function getSedeId()
    {
        return $this->sede_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Proceso
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
     * Add EstadoFlujo entity to collection (one to many).
     *
     * @param \App\Entity\EstadoFlujo $estadoFlujo
     * @return \App\Entity\Proceso
     */
    public function addEstadoFlujo(EstadoFlujo $estadoFlujo)
    {
        $this->estadoFlujos[] = $estadoFlujo;

        return $this;
    }

    /**
     * Remove EstadoFlujo entity from collection (one to many).
     *
     * @param \App\Entity\EstadoFlujo $estadoFlujo
     * @return \App\Entity\Proceso
     */
    public function removeEstadoFlujo(EstadoFlujo $estadoFlujo)
    {
        $this->estadoFlujos->removeElement($estadoFlujo);

        return $this;
    }

    /**
     * Get EstadoFlujo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstadoFlujos()
    {
        return $this->estadoFlujos;
    }

    /**
     * Add Usuario entity to collection (one to many).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Proceso
     */
    public function addUsuario(Usuario $usuario)
    {
        $this->usuarios[] = $usuario;

        return $this;
    }

    /**
     * Remove Usuario entity from collection (one to many).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Proceso
     */
    public function removeUsuario(Usuario $usuario)
    {
        $this->usuarios->removeElement($usuario);

        return $this;
    }

    /**
     * Get Usuario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Set Sede entity (many to one).
     *
     * @param \App\Entity\Sede $sede
     * @return \App\Entity\Proceso
     */
    public function setSede(Sede $sede = null)
    {
        $this->sede = $sede;

        return $this;
    }

    /**
     * Get Sede entity (many to one).
     *
     * @return \App\Entity\Sede
     */
    public function getSede()
    {
        return $this->sede;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'codigo_interno' => $this->getCodigoInterno(),
            'direccion_oficina' => $this->getDireccionOficina(),
            'extension' => $this->getExtension(),
            'telefono_fijo_oficina' => $this->getTelefonoFijoOficina(),
        ];
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'codigo_interno', 'direccion_oficina', 'extension', 'telefono_fijo_oficina', 'sede_id', 'estado_id');
    }
}
