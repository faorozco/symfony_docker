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
 *         "path"="/plantillas_version",
 *         "controller"=TemplateCreate::class,
 *         "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put"={
 *         "method"="PUT",
 *         "path"="/plantillas_version",
 *         "controller"=TemplateUpdate::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "get"={
 *         "method"="GET",
 *         "path"="/plantillas_version/{id}",
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
 * App\Entity\PlantillaVersion
 *
 * @ORM\Entity()
 * @ORM\Table(name="plantilla_version", indexes={@ORM\Index(name="fk_flujo_trabajo_version_copy1_formulario_version1_idx", columns={"formulario_version_id"})})
 */
class PlantillaVersion
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
    protected $formulario_version_id;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $plantilla_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="FormatoVersion", mappedBy="plantillaVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="plantilla_version_id", nullable=false)
     */
    protected $formatos;

    /**
     * @ORM\ManyToOne(targetEntity="FormularioVersion", inversedBy="plantillasVersion")
     * @ORM\JoinColumn(name="formulario_version_id", referencedColumnName="id", nullable=false)
     */
    protected $formularioVersion;

    public function __construct()
    {
        $this->formatos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\PlantillaVersion
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
     * @return \App\Entity\PlantillaVersion
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
     * @return \App\Entity\PlantillaVersion
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
     * Set the value of formulario_version_id.
     *
     * @param integer $formulario_version_id
     * @return \App\Entity\PlantillaVersion
     */
    public function setFormularioVersionId($formulario_version_id)
    {
        $this->formulario_version_id = $formulario_version_id;

        return $this;
    }

    /**
     * Get the value of formulario_version_id.
     *
     * @return integer
     */
    public function getFormularioVersionId()
    {
        return $this->formulario_version_id;
    }

    /**
     * Set the value of plantilla_id.
     *
     * @param integer $plantilla_id
     * @return \App\Entity\PlantillaVersion
     */
    public function setPlantillaId($plantilla_id)
    {
        $this->plantilla_id = $plantilla_id;

        return $this;
    }

    /**
     * Get the value of plantilla_id.
     *
     * @return integer
     */
    public function getPlantillaId()
    {
        return $this->plantilla_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\PlantillaVersion
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
     * Add Formato entity to collection (one to many).
     *
     * @param \App\Entity\Formato $formato
     * @return \App\Entity\PlantillaVersion
     */
    public function addFormato(Formato $formato)
    {
        $this->formatos[] = $formato;

        return $this;
    }

    /**
     * Remove Formato entity from collection (one to many).
     *
     * @param \App\Entity\Formato $formato
     * @return \App\Entity\PlantillaVersion
     */
    public function removeFormato(Formato $formato)
    {
        $this->formatos->removeElement($formato);

        return $this;
    }

    /**
     * Get Formato entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormatos()
    {
        return $this->formatos;
    }

    /**
     * Set FormularioVersion entity (many to one).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\PlantillaVersion
     */
    public function setFormularioVersion(FormularioVersion $formularioVersion = null)
    {
        $this->formularioVersion = $formularioVersion;

        return $this;
    }

    /**
     * Get FormularioVersion entity (many to one).
     *
     * @return \App\Entity\FormularioVersion
     */
    public function getFormularioVersion()
    {
        return $this->formularioVersion;
    }

    public function __sleep()
    {
        return array('id', 'descripcion', 'contenido', 'formulario_version_id', 'estado_id');
    }
}
