<?php



namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

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
 *         "path"="/tipo_contactos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"descripcion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"descripcion"},
 *      arguments={"orderParameterName"="order"})
 * 
 * App\Entity\TipoContacto
 *
 * @ORM\Entity()
 * @ORM\Table(name="tipo_contacto")
 */
class TipoContacto
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
    protected $descripcion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="Contacto", mappedBy="tipoContacto")
     * @ORM\JoinColumn(name="id", referencedColumnName="tipo_contacto_id", nullable=false)
     */
    protected $contactos;

    public function __construct()
    {
        $this->contactos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\TipoContacto
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
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\TipoContacto
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\TipoContacto
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
     * @return \App\Entity\TipoContacto
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
     * @return \App\Entity\TipoContacto
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

    public function __sleep()
    {
        return array('id', 'descripcion', 'estado_id');
    }
}