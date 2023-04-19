<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\FormPermite;
use App\Controller\OpcionFormDelete;
use App\Controller\OpcionFormMany;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "crearopcionesformulario"={
 *          "method"="POST",
 *          "path"="/opcion_formularios_many",
 *          "controller"=OpcionFormMany::class,
 *          "defaults"={"_api_receive"=false}
 *          },
 *      "cargarpermitesformulario"={
 *          "method"="GET",
 *          "path"="/permites_formularios/{id}",
 *          "controller"=FormPermite::class,
 *          "requirements"={"id"="\d+"}
 *          },
 *      "cargarpermitesformulariobyregistro"={
 *          "method"="GET",
 *          "path"="/permites_formularios/{id}/registros/{registro_id}",
 *          "controller"=FormPermite::class,
 *          "requirements"={"id"="\d+"}
 *          },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/opcion?formlarios/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "eliminaropcionesformulario"={
 *          "method"="DELETE",
 *          "path"="/opcion_formularios_delete/{id_form}",
 *          "controller"=OpcionFormDelete::class,
 *          "defaults"={"_api_receive"=false},
 *          "status"=200,
 *          },
 *  }
 * )
 * App\Entity\OpcionFormulario
 *
 * @ORM\Entity()
 * @ORM\Table(name="opcion_formulario", indexes={@ORM\Index(name="fk_opcionformulario_formulario1_idx", columns={"formulario_id"}), @ORM\Index(name="fk_opcion_formulario_permite1_idx", columns={"permite_id"})})
 */
class OpcionFormulario
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $formulario_id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $grupos;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $configuraciones;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $acciones;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $acciones_grupo;

    /**
     * @ORM\Column(type="integer")
     */
    protected $permite_id;

    /**
     * @ORM\ManyToOne(targetEntity="Formulario", inversedBy="opcionFormularios")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=false)
     */
    protected $formulario;

    /**
     * @ORM\ManyToOne(targetEntity="Permite", inversedBy="opcionFormularios")
     * @ORM\JoinColumn(name="permite_id", referencedColumnName="id", nullable=false)
     */
    protected $permite;

    /**
     * @ORM\OneToMany(targetEntity="OpcionFormularioVersion", mappedBy="opcionFormulario")
     * @ORM\JoinColumn(name="id", referencedColumnName="opcion_formulario_id", nullable=true)
     */
    protected $opcionFormulariosVersion;

    public function __construct()
    {
        $this->opcionFormulariosVersion = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\OpcionFormulario
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
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\OpcionFormulario
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
     * Set the value of configuraciones.
     *
     * @param string $configuraciones
     * @return \App\Entity\OpcionFormulario
     */
    public function setConfiguraciones($configuraciones)
    {
        $this->configuraciones = $configuraciones;

        return $this;
    }

    /**
     * Get the value of configuraciones.
     *
     * @return string
     */
    public function getConfiguraciones()
    {
        return $this->configuraciones;
    }

    /**
     * Set the value of configuraciones.
     *
     * @param string $acciones
     * @return \App\Entity\OpcionFormulario
     */
    public function setAcciones($acciones)
    {
        $this->acciones = $acciones;

        return $this;
    }

    /**
     * Get the value of configuraciones.
     *
     * @return string
     */
    public function getAcciones()
    {
        return $this->acciones;
    }

    /**
     * Set the value of permite_id.
     *
     * @param integer $permite_id
     * @return \App\Entity\OpcionFormulario
     */
    public function setPermiteId($permite_id)
    {
        $this->permite_id = $permite_id;

        return $this;
    }

    /**
     * Get the value of permite_id.
     *
     * @return integer
     */
    public function getPermiteId()
    {
        return $this->permite_id;
    }

    /**
     * Set Formulario entity (many to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\OpcionFormulario
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

    /**
     * Set Permite entity (many to one).
     *
     * @param \App\Entity\Permite $permite
     * @return \App\Entity\OpcionFormulario
     */
    public function setPermite(Permite $permite = null)
    {
        $this->permite = $permite;

        return $this;
    }

    /**
     * Get Permite entity (many to one).
     *
     * @return \App\Entity\Permite
     */
    public function getPermite()
    {
        return $this->permite;
    }

     /**
     * Add OpcionFormularioVersion entity to collection (one to many).
     *
     * @param \App\Entity\OpcionFormularioVersion $opcionFormulario
     * @return \App\Entity\OpcionFormulario
     */
    public function addOpcionFormularioVersion(OpcionFormularioVersion $opcionFormularioVersion)
    {
        $this->opcionFormulariosVersion[] = $opcionFormularioVersion;

        return $this;
    }

    /**
     * Remove OpcionFormularioVersion entity from collection (one to many).
     *
     * @param \App\Entity\OpcionFormularioVersion $opcionFormularioVersion
     * @return \App\Entity\OpcionFormulario
     */
    public function removeOpcionFormularioVersion(OpcionFormularioVersion $opcionFormularioVersion)
    {
        $this->opcionFormulariosVersion->removeElement($opcionFormularioVersion);

        return $this;
    }

    /**
     * Get OpcionFormularioVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpcionFormulariosVersion()
    {
        return $this->opcionFormulariosVersion;
    }

    public function __sleep()
    {
        return array('id', 'formulario_id', 'configuraciones', 'acciones', 'permite_id');
    }
}
