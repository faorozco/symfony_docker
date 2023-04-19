<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\TemplateUpdate;
use App\Controller\TemplateCreate;



/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
 *         "method"="POST",
 *         "path"="/plantillas",
 *         "controller"=TemplateCreate::class,
 *         "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put"={
 *         "method"="PUT",
 *         "path"="/plantillas",
 *         "controller"=TemplateUpdate::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "get"={
 *         "method"="GET",
 *         "path"="/plantillas/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"descripcion","contenido"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"id","descripcion"},
 *      arguments={"orderParameterName"="order"}
 * )
 *
 * App\Entity\Plantilla
 *
 * @ORM\Entity()
 * @ORM\Table(name="plantilla", indexes={@ORM\Index(name="fk_flujo_trabajo_copy1_formulario1_idx", columns={"formulario_id"})})
 */
class Plantilla
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $contenido;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $formulario_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="Formulario", inversedBy="plantillas")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=false)
     */
    protected $formulario;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Plantilla
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
     * @return \App\Entity\Plantilla
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
     * Set the value of contenido.
     *
     * @param string $contenido
     * @return \App\Entity\Plantilla
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get the value of contenido.
     *
     * @return string
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\Plantilla
     */
    public function setFormularioId($formulario_id)
    {
        $this->formulario_id = $formulario_id;

        return $this;
    }

    /**
     * Get the value of formulario_id.
     *
     * @return integer
     */
    public function getFormularioId()
    {
        return $this->formulario_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Plantilla
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
     * Set Formulario entity (many to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\Plantilla
     */
    public function setFormulario(Formulario $formulario = null)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Get Formulario entity (many to one).
     *
     * @return \App\Entity\Formulario
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    public function __sleep()
    {
        return array('id', 'descripcion', 'contenido', 'formulario_id', 'estado_id');
    }
}
