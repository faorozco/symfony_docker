<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\Ciudad\CiudadListar;
use App\Controller\Ciudad\CiudadListarPage;

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
 *         "path"="/ciudads/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "list"={
 *         "method"="GET",
 *         "path"="/ciudads/list",
 *         "controller"=CiudadListar::class,
 *         "defaults"={"_api_receive"=false}
 *        },
 *      "listPage"={
 *         "method"="GET",
 *         "path"="/ciudads/list-page",
 *         "controller"=CiudadListarPage::class,
 *         "defaults"={"_api_receive"=false}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"codigo","nombre"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"codigo", "nombre"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Ciudad
 *
 * @ORM\Entity(repositoryClass="App\Repository\CiudadRepository")
 * @ORM\Table(name="ciudad")
 */
class Ciudad
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $codigo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="Contacto", mappedBy="ciudad")
     * @ORM\JoinColumn(name="id", referencedColumnName="ciudad_id", nullable=false)
     */
    protected $contactos;

    /**
     * @ORM\OneToMany(targetEntity="Tercero", mappedBy="ciudad")
     * @ORM\JoinColumn(name="id", referencedColumnName="ciudad_id", nullable=false)
     */
    protected $terceros;

    public function __construct()
    {
        $this->contactos = new ArrayCollection();
        $this->terceros = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Ciudad
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
     * @return \App\Entity\Ciudad
     *  @ORM\PostPersist
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
     * Get the value of codigo
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set the value of codigo
     *
     * @return  self
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Ciudad
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
     * Add Contacto entity to collection (one to many).
     *
     * @param \App\Entity\Contacto $contacto
     * @return \App\Entity\Ciudad
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
     * @return \App\Entity\Ciudad
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
     * Add Tercero entity to collection (one to many).
     *
     * @param \App\Entity\Tercero $tercero
     * @return \App\Entity\Ciudad
     */
    public function addTercero(Tercero $tercero)
    {
        $this->terceros[] = $tercero;

        return $this;
    }

    /**
     * Remove Tercero entity from collection (one to many).
     *
     * @param \App\Entity\Tercero $tercero
     * @return \App\Entity\Ciudad
     */
    public function removeTercero(Tercero $tercero)
    {
        $this->terceros->removeElement($tercero);

        return $this;
    }

    /**
     * Get Tercero entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerceros()
    {
        return $this->terceros;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'codigo', 'estado_id');
    }
}
