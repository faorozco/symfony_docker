<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *  attributes={"pagination_enabled"=false},
 *   collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *   },
 *   itemOperations={
 *       "put",
 *       "get"={
 *         "method"="GET",
 *         "path"="/componentes/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *   }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre"},
 *      arguments={"orderParameterName"="order"}
 *  )
 *
 * App\Entity\Componente
 *
 * @ORM\Entity()
 * @ORM\Table(name="componente")
 */
class Componente
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tipo_componente;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $ayuda;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $link;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToMany(targetEntity="Rol", mappedBy="componentes")
     */
    protected $rols;

    public function __construct()
    {
        $this->rols = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Componente
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
     * @return \App\Entity\Componente
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
     * Set the value of tipo_componente.
     *
     * @param integer $tipo_componente
     * @return \App\Entity\Componente
     */
    public function setTipoComponente($tipo_componente)
    {
        $this->tipo_componente = $tipo_componente;

        return $this;
    }

    /**
     * Get the value of tipo_componente.
     *
     * @return integer
     */
    public function getTipoComponente()
    {
        return $this->tipo_componente;
    }

    /**
     * Set the value of ayuda.
     *
     * @param string $ayuda
     * @return \App\Entity\Componente
     */
    public function setAyuda($ayuda)
    {
        $this->ayuda = $ayuda;

        return $this;
    }

    /**
     * Get the value of ayuda.
     *
     * @return string
     */
    public function getAyuda()
    {
        return $this->ayuda;
    }

    /**
     * Set the value of link.
     *
     * @param string $link
     * @return \App\Entity\Componente
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get the value of link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Componente
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
     * Add Rol entity to collection (one to many).
     *
     * @param \App\Entity\Rol $rol
     * @return \App\Entity\Componente
     */
    public function addRol(Rol $rol)
    {
        $this->rols[] = $rol;

        return $this;
    }

    /**
     * Remove Rol entity from collection (one to many).
     *
     * @param \App\Entity\Rol $rol
     * @return \App\Entity\Componente
     */
    public function removeRol(Rol $rol)
    {
        $this->rols->removeElement($rol);

        return $this;
    }

    /**
     * Get Rol entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getrols()
    {
        return $this->rols;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'tipo_componente', 'ayuda', 'link', 'estado_id');
    }
}
