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
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/valor_documentals/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"tipo","descripcion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"tipo","descripcion"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\Valordocumental
 *
 * @ORM\Entity()
 * @ORM\Table(name="valor_documental")
 */
class ValorDocumental
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $tipo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToMany(targetEntity="TablaRetencion", mappedBy="valorDocumentals")
     */
    protected $tablaRetencions;

    public function __construct()
    {
        $this->tablaRetencions = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Valordocumental
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
     * Set the value of tipo.
     *
     * @param string $tipo
     * @return \App\Entity\Valordocumental
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of tipo.
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\Valordocumental
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
     * @return \App\Entity\Valordocumental
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
     * Add TablaRetencion entity to collection (one to many).
     *
     * @param \App\Entity\TablaRetencion $tablaRetencion
     * @return \App\Entity\Valordocumental
     */
    public function addTablaRetencions(TablaRetencion $tablaRetencion)
    {
        $this->tablaRetencions[] = $tablaRetencion;

        return $this;
    }

    /**
     * Remove TablaRetencion entity from collection (one to many).
     *
     * @param \App\Entity\TablaRetencion $tablaRetencion
     * @return \App\Entity\Valordocumental
     */
    public function removeTablaRetencions(TablaRetencion $tablaRetencion)
    {
        $this->tablaRetencions->removeElement($tablaRetencion);

        return $this;
    }

    /**
     * Get TrdValordocumental entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTablaRetencions()
    {
        return $this->tablaRetencions;
    }

    public function __sleep()
    {
        return array('id', 'tipo', 'descripcion', 'estado_id');
    }
}
