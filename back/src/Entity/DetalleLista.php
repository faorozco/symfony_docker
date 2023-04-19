<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    attributes={"pagination_enabled"=false},
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/detalle_listas/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"codigo","descripcion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"codigo","descripcion"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\DetalleLista
 *
 * @ORM\Entity()
 * @ORM\Table(name="detalle_lista", indexes={@ORM\Index(name="fk_detalle_lista_Lista1_idx", columns={"lista_id"})})
 */
class DetalleLista
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
    protected $codigo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    protected $lista_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="RegistroLista", mappedBy="detalleLista")
     * @ORM\JoinColumn(name="id", referencedColumnName="detalle_lista_id", nullable=false)
     */
    protected $registroListas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroMultiseleccion", mappedBy="detalleLista")
     * @ORM\JoinColumn(name="id", referencedColumnName="detalle_lista_id", nullable=false)
     */
    protected $registroMultiseleccions;

    /**
     * @ORM\ManyToOne(targetEntity="Lista", inversedBy="detalleListas")
     * @ORM\JoinColumn(name="lista_id", referencedColumnName="id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $lista;

    public function __construct()
    {
        $this->registroListas = new ArrayCollection();
        $this->registroMultiseleccions = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\DetalleLista
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
     * Set the value of codigo.
     *
     * @param string $codigo
     * @return \App\Entity\DetalleLista
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get the value of codigo.
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\DetalleLista
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
     * Set the value of lista_id.
     *
     * @param integer $lista_id
     * @return \App\Entity\DetalleLista
     */
    public function setListaId($lista_id)
    {
        $this->lista_id = $lista_id;

        return $this;
    }

    /**
     * Get the value of lista_id.
     *
     * @return integer
     */
    public function getListaId()
    {
        return $this->lista_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\DetalleLista
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
     * Add RegistroLista entity to collection (one to many).
     *
     * @param \App\Entity\RegistroLista $registroLista
     * @return \App\Entity\DetalleLista
     */
    public function addRegistroLista(RegistroLista $registroLista)
    {
        $this->registroListas[] = $registroLista;

        return $this;
    }

    /**
     * Remove RegistroLista entity from collection (one to many).
     *
     * @param \App\Entity\RegistroLista $registroLista
     * @return \App\Entity\DetalleLista
     */
    public function removeRegistroLista(RegistroLista $registroLista)
    {
        $this->registroListas->removeElement($registroLista);

        return $this;
    }

    /**
     * Get RegistroLista entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroListas()
    {
        return $this->registroListas;
    }

    /**
     * Add RegistroMultiseleccion entity to collection (one to many).
     *
     * @param \App\Entity\RegistroMultiseleccion $registroMultiseleccion
     * @return \App\Entity\DetalleLista
     */
    public function addRegistroMultiseleccion(RegistroMultiseleccion $registroMultiseleccion)
    {
        $this->registroMultiseleccions[] = $registroMultiseleccion;

        return $this;
    }

    /**
     * Remove RegistroMultiseleccion entity from collection (one to many).
     *
     * @param \App\Entity\RegistroMultiseleccion $registroMultiseleccion
     * @return \App\Entity\DetalleLista
     */
    public function removeRegistroMultiseleccion(RegistroMultiseleccion $registroMultiseleccion)
    {
        $this->registroMultiseleccions->removeElement($registroMultiseleccion);

        return $this;
    }

    /**
     * Get RegistroMultiseleccion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroMultiseleccions()
    {
        return $this->registroMultiseleccions;
    }

    /**
     * Set Lista entity (many to one).
     *
     * @param \App\Entity\Lista $lista
     * @return \App\Entity\DetalleLista
     */
    public function setLista(Lista $lista = null)
    {
        $this->lista = $lista;

        return $this;
    }

    /**
     * Get Lista entity (many to one).
     *
     * @return \App\Entity\Lista
     */
    public function getLista()
    {
        return $this->lista;
    }

    public function __sleep()
    {
        return array('id', 'codigo', 'descripcion', 'lista_id', 'estado_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'codigo' => $this->getCodigo(),
            'descripcion' => $this->getDescripcion(),
            'lista_id' => $this->getListaId(),
            'estado_id' => $this->getEstadoId(),
        ];
    }
}