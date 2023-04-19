<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\Contacto\ContactoExport;
use App\Controller\Contacto\ContactoImport;
use App\Controller\Contacto\ContactoCreate;
use App\Controller\Contacto\ContactoDownloadModel;
use App\Filter\ORSearchFilter;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
*          "method"="POST",
 *         "path"="/contactos/create",
 *         "controller"=ContactoCreate::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "import"={
 *         "method"="POST",
 *         "path"="/contactos/import",
 *         "controller"=ContactoImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *     "export"={
 *          "method"="POST",
 *          "path"="/contactos/export",
 *          "controller"=ContactoExport::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *     "downloadmodel"={
 *          "method"="POST",
 *          "path"="/contactos/downloadmodel",
 *          "controller"=ContactoDownloadModel::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/contactos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre","tratamiento","telefono_fijo","celular","correo","comentario"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre","tratamiento","telefono_fijo","celular","correo","comentario"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Contacto
 *
 * @ORM\Entity()
 * @ORM\Table(name="contacto", indexes={@ORM\Index(name="fk_contacto_tercero1_idx", columns={"tercero_id"}), @ORM\Index(name="fk_contacto_ciudad1_idx", columns={"ciudad_id"}), @ORM\Index(name="fk_contacto_tipo_contacto1_idx", columns={"tipo_contacto_id"})})
 */
class Contacto
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $tratamiento;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $telefono_fijo;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $celular;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $correo;

    /**
     * @ORM\Column(type="string", length=3000, nullable=true)
     */
    protected $comentario;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tercero_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ciudad_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tipo_contacto_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $cargo;

    /**
     * @ORM\ManyToOne(targetEntity="Tercero", inversedBy="contactos")
     * @ORM\JoinColumn(name="tercero_id", referencedColumnName="id", nullable=false)
     */
    protected $tercero;

    /**
     * @ORM\ManyToOne(targetEntity="Ciudad", inversedBy="contactos")
     * @ORM\JoinColumn(name="ciudad_id", referencedColumnName="id", nullable=false)
     */
    protected $ciudad;

    /**
     * @ORM\ManyToOne(targetEntity="TipoContacto", inversedBy="contactos")
     * @ORM\JoinColumn(name="tipo_contacto_id", referencedColumnName="id", nullable=false)
     */
    protected $tipoContacto;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Contacto
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
     * @return \App\Entity\Contacto
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
     * Set the value of tratamiento.
     *
     * @param string $tratamiento
     * @return \App\Entity\Contacto
     */
    public function setTratamiento($tratamiento)
    {
        $this->tratamiento = $tratamiento;

        return $this;
    }

    /**
     * Get the value of tratamiento.
     *
     * @return string
     */
    public function getTratamiento()
    {
        return $this->tratamiento;
    }

    /**
     * Set the value of telefono_fijo.
     *
     * @param string $telefono_fijo
     * @return \App\Entity\Contacto
     */
    public function setTelefonoFijo($telefono_fijo)
    {
        $this->telefono_fijo = $telefono_fijo;

        return $this;
    }

    /**
     * Get the value of telefono_fijo.
     *
     * @return string
     */
    public function getTelefonoFijo()
    {
        return $this->telefono_fijo;
    }

    /**
     * Set the value of celular.
     *
     * @param integer $celular
     * @return \App\Entity\Contacto
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
     * Set the value of correo.
     *
     * @param string $correo
     * @return \App\Entity\Contacto
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get the value of correo.
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set the value of comentario.
     *
     * @param string $comentario
     * @return \App\Entity\Contacto
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get the value of comentario.
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set the value of tercero_id.
     *
     * @param integer $tercero_id
     * @return \App\Entity\Contacto
     */
    public function setTerceroId($tercero_id)
    {
        $this->tercero_id = $tercero_id;

        return $this;
    }

    /**
     * Get the value of tercero_id.
     *
     * @return integer
     */
    public function getTerceroId()
    {
        return $this->tercero_id;
    }

    /**
     * Set the value of ciudad_id.
     *
     * @param integer $ciudad_id
     * @return \App\Entity\Contacto
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
     * Set the value of tipo_contacto_id.
     *
     * @param integer $tipo_contacto_id
     * @return \App\Entity\Contacto
     */
    public function setTipoContactoId($tipo_contacto_id)
    {
        $this->tipo_contacto_id = $tipo_contacto_id;

        return $this;
    }

    /**
     * Get the value of tipo_contacto_id.
     *
     * @return integer
     */
    public function getTipoContactoId()
    {
        return $this->tipo_contacto_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Contacto
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
     * Get the value of cargo
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set the value of cargo
     *
     * @return  self
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Set Tercero entity (many to one).
     *
     * @param \App\Entity\Tercero $tercero
     * @return \App\Entity\Contacto
     */
    public function setTercero(Tercero $tercero = null)
    {
        $this->tercero = $tercero;

        return $this;
    }

    /**
     * Get Tercero entity (many to one).
     *
     * @return \App\Entity\Tercero
     */
    public function getTercero()
    {
        return $this->tercero;
    }

    /**
     * Set Ciudad entity (many to one).
     *
     * @param \App\Entity\Ciudad $ciudad
     * @return \App\Entity\Contacto
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

    /**
     * Set TipoContacto entity (many to one).
     *
     * @param \App\Entity\TipoContacto $tipoContacto
     * @return \App\Entity\Contacto
     */
    public function setTipoContacto(TipoContacto $tipoContacto = null)
    {
        $this->tipoContacto = $tipoContacto;

        return $this;
    }

    /**
     * Get TipoContacto entity (many to one).
     *
     * @return \App\Entity\TipoContacto
     */
    public function getTipoContacto()
    {
        return $this->tipoContacto;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'cargo', 'tratamiento', 'telefono_fijo', 'celular', 'correo', 'comentario', 'tercero_id', 'ciudad_id', 'tipo_contacto_id', 'estado_id');
    }
}
