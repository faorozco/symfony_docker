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
 *         "path"="/tipo_documentals/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"codigo_archivo", "descripcion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"codigo_archivo", "descripcion"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\TipoDocumental
 *
 * @ORM\Entity()
 * @ORM\Table(name="tipo_documental")
 */
class TipoDocumental
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
    protected $codigo_archivo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

     /**
     * @ORM\OneToMany(targetEntity="TablaRetencionVersion", mappedBy="tipoDocumental")
     * @ORM\JoinColumn(name="id", referencedColumnName="tipo_documental_id", nullable=false)
     */
    protected $tablaRetencionsVersion;

    public function __construct()
    {
        $this->tablaRetencionsVersion = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\TipoDocumental
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
     * Set the value of codigo_archivo.
     *
     * @param string $codigo_archivo
     * @return \App\Entity\TipoDocumental
     */
    public function setCodigoArchivo($codigo_archivo)
    {
        $this->codigo_archivo = $codigo_archivo;

        return $this;
    }

    /**
     * Get the value of codigo_archivo.
     *
     * @return string
     */
    public function getCodigoArchivo()
    {
        return $this->codigo_archivo;
    }

    /**
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\TipoDocumental
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
     * @return \App\Entity\TipoDocumental
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
     * Add TablaRetencionVersion entity to collection (one to many).
     *
     * @param \App\Entity\TablaRetencionVersion $tablaRetencionVersion
     * @return \App\Entity\TipoDocumental
     */
    public function addTablaRetencionVersion(TablaRetencionVersion $tablaRetencionVersion)
    {
        $this->tablaRetencionsVersion[] = $tablaRetencionVersion;

        return $this;
    }

    /**
     * Remove TablaRetencionVersion entity from collection (one to many).
     *
     * @param \App\Entity\TablaRetencionVersion $tablaRetencionVersion
     * @return \App\Entity\TipoDocumental
     */
    public function removeTablaRetencionVersion(TablaRetencionVersion $tablaRetencionVersion)
    {
        $this->tablaRetencionsVersion->removeElement($tablaRetencionVersion);

        return $this;
    }

    /**
     * Get TablaRetencionVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTablaRetencionsVersion()
    {
        return $this->tablaRetencionsVersion;
    }

    public function __sleep()
    {
        return array('id', 'codigo_archivo', 'descripcion', 'estado_id');
    }
}
