<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\Tercero\TerceroExport;
use App\Controller\Tercero\TerceroImport;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\Tercero\TerceroCreate;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "export"={
 *          "method"="POST",
 *          "path"="/terceros/export",
 *          "controller"=TerceroExport::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "import"={
 *         "method"="POST",
 *         "path"="/terceros/import",
 *         "controller"=TerceroImport::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *      "create"={
 *         "method"="POST",
 *         "path"="/terceros/create",
 *         "controller"=TerceroCreate::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/terceros/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"identificacion", "nombre", "direccion", "telefono", "celular", "correo_electronico"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"identificacion", "nombre", "direccion", "telefono", "celular", "correo_electronico"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\Tercero
 *
 * @ORM\Entity()
 * @UniqueEntity(
 *     fields={"identificacion","estado_id"},
 *     errorPath="identificacion",
 *     message="Ya registrada"
 * )
 * @ORM\Table(
 *      name="tercero", 
 *      indexes={@ORM\Index(name="fk_tercero_ciudad1_idx", columns={"ciudad_id"})})
 */
class Tercero
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, unique=true)
     */
    protected $identificacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $direccion;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $telefono;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $celular;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ciudad_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $correo_electronico;

    /**
     * @ORM\OneToMany(targetEntity="Contacto", mappedBy="tercero")
     * @ORM\JoinColumn(name="id", referencedColumnName="tercero_id", nullable=false)
     */
    protected $contactos;

    /**
     * @ORM\ManyToOne(targetEntity="Ciudad", inversedBy="terceros")
     * @ORM\JoinColumn(name="ciudad_id", referencedColumnName="id", nullable=false)
     */
    protected $ciudad;

    public function __construct()
    {
        $this->contactos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Tercero
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
     * Set the value of identificacion.
     *
     * @param string $identificacion
     * @return \App\Entity\Tercero
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;

        return $this;
    }

    /**
     * Get the value of identificacion.
     *
     * @return string
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\Tercero
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
     * Set the value of direccion.
     *
     * @param string $direccion
     * @return \App\Entity\Tercero
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get the value of direccion.
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set the value of telefono.
     *
     * @param string $telefono
     * @return \App\Entity\Tercero
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get the value of telefono.
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set the value of celular.
     *
     * @param integer $celular
     * @return \App\Entity\Tercero
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get the value of celular.
     *
     * @return integer
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set the value of ciudad_id.
     *
     * @param integer $ciudad_id
     * @return \App\Entity\Tercero
     */
    public function setCiudadId($ciudad_id)
    {
        $this->ciudad_id = $ciudad_id;

        return $this;
    }

    /**
     * Get the value of ciudad_id.
     *
     * @return integer
     */
    public function getCiudadId()
    {
        return $this->ciudad_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Tercero
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
     * Set the value of correo_electronico.
     *
     * @param string $correo_electronico
     * @return \App\Entity\Tercero
     */
    public function setCorreoElectronico($correo_electronico)
    {
        $this->correo_electronico = $correo_electronico;

        return $this;
    }

    /**
     * Get the value of correo_electronico.
     *
     * @return string
     */
    public function getCorreoElectronico()
    {
        return $this->correo_electronico;
    }

    /**
     * Add Contacto entity to collection (one to many).
     *
     * @param \App\Entity\Contacto $contacto
     * @return \App\Entity\Tercero
     */
    public function addContacto(Contacto $contacto)
    {
        $this->contactos[] = $contacto;

        return $this;
    }

    /**
     * Remove Contacto entity from collection (one to many).
     *
     * @param \App\Entity\Contacto $contacto
     * @return \App\Entity\Tercero
     */
    public function removeContacto(Contacto $contacto)
    {
        $this->contactos->removeElement($contacto);

        return $this;
    }

    /**
     * Get Contacto entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContactos()
    {
        return $this->contactos;
    }

    /**
     * Set Ciudad entity (many to one).
     *
     * @param \App\Entity\Ciudad $ciudad
     * @return \App\Entity\Tercero
     */
    public function setCiudad(Ciudad $ciudad = null)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get Ciudad entity (many to one).
     *
     * @return \App\Entity\Ciudad
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    public function __sleep()
    {
        return array('id', 'identificacion', 'nombre', 'direccion', 'telefono', 'celular', 'ciudad_id', 'estado_id', 'correo_electronico');
    }
}
