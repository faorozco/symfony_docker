<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *    normalizationContext={"groups"={"get"}},
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/entidad_auditorias/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre","descripcion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre","descripcion"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Entidad
 *
 * @ORM\Entity()
 * @ORM\Table(name="entidad_auditoria")
 */
class EntidadAuditoria
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("get")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups("get")
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups("get")
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("get")
     */
    protected $campo_visualizar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("get")
     */
    protected $campo_busqueda;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("get")
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="CampoFormulario", mappedBy="entidad")
     * @ORM\JoinColumn(name="id", referencedColumnName="entidad_id", nullable=false)
     */
    protected $campoFormularios;

    public function __construct()
    {
        $this->campoFormularios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Entidad
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
     * @return \App\Entity\Entidad
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
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\Entidad
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
     * Set the value of campo_visualizar.
     *
     * @param string $campo_visualizar
     * @return \App\Entity\Entidad
     */
    public function setCampoVisualizar($campo_visualizar)
    {
        $this->campo_visualizar = $campo_visualizar;

        return $this;
    }

    /**
     * Get the value of campo_visualizar.
     *
     * @return string
     */
    public function getCampoVisualizar()
    {
        return $this->campo_visualizar;
    }

    /**
     * Get the value of campo_busqueda
     */
    public function getCampoBusqueda()
    {
        return $this->campo_busqueda;
    }

    /**
     * Set the value of campo_busqueda
     *
     * @return  self
     */
    public function setCampoBusqueda($campo_busqueda)
    {
        $this->campo_busqueda = $campo_busqueda;

        return $this;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Entidad
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
     * Add CampoFormulario entity to collection (one to many).
     *
     * @param \App\Entity\CampoFormulario $campoFormulario
     * @return \App\Entity\Entidad
     */
    public function addCampoFormulario(CampoFormulario $campoFormulario)
    {
        $this->campoFormularios[] = $campoFormulario;

        return $this;
    }

    /**
     * Remove CampoFormulario entity from collection (one to many).
     *
     * @param \App\Entity\CampoFormulario $campoFormulario
     * @return \App\Entity\Entidad
     */
    public function removeCampoFormulario(CampoFormulario $campoFormulario)
    {
        $this->campoFormularios->removeElement($campoFormulario);

        return $this;
    }

    /**
     * Get CampoFormulario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCampoFormularios()
    {
        return $this->campoFormularios;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'descripcion', 'campo_visualizar', 'estado_id');
    }
}
