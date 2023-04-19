<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\FormPermite;
use App\Controller\OpcionFormDelete;
use App\Controller\OpcionFormMany;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "crearopcionesformulario"={
 *          "method"="POST",
 *          "path"="/opcion_formularios_version_many",
 *          "controller"=OpcionFormMany::class,
 *          "defaults"={"_api_receive"=false}
 *          },
 *      "cargarpermitesformulario"={
 *          "method"="GET",
 *          "path"="/permites_formularios_version/{id}",
 *          "controller"=FormPermite::class,
 *          "requirements"={"id"="\d+"}
 *          },
 *      "cargarpermitesformulariobyregistro"={
 *          "method"="GET",
 *          "path"="/permites_formularios_version/{id}/registros/{registro_id}",
 *          "controller"=FormPermite::class,
 *          "requirements"={"id"="\d+"}
 *          },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/opcion_formlarios_version/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "eliminaropcionesformulario"={
 *          "method"="DELETE",
 *          "path"="/opcion_formularios_version_delete/{id_form}",
 *          "controller"=OpcionFormDelete::class,
 *          "defaults"={"_api_receive"=false},
 *          "status"=200,
 *          },
 *  }
 * )
 * App\Entity\OpcionFormularioVersion
 *
 * @ORM\Entity()
 * @ORM\Table(name="opcion_formulario_version", indexes={@ORM\Index(name="fk_opcionformularioversion_formulario1_idx", columns={"formulario_version_id"})})
 */
class OpcionFormularioVersion
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
    protected $formulario_version_id;

    /**
     * @ORM\ManyToOne(targetEntity="FormularioVersion", inversedBy="opcionFormulariosVersion")
     * @ORM\JoinColumn(name="formulario_version_id", referencedColumnName="id", nullable=false)
     */
    protected $formularioVersion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $opcion_formulario_id;

    /**
     * @ORM\ManyToOne(targetEntity="OpcionFormulario")
     * @ORM\JoinColumn(name="opcion_formulario_id", referencedColumnName="id", nullable=true)
     */
    protected $opcionFormulario;

    public function __construct()
    {

    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\OpcionFormularioVersion
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
     * Set the value of formulario_version_id.
     *
     * @param integer $formulario_version_id
     * @return \App\Entity\OpcionFormularioVersion
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
     * Set Formulario entity (many to one).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\OpcionFormularioVersion
     */
    public function setFormularioVersion(FormularioVersion $formularioVersion = null)
    {
        $this->formularioVersion = $formularioVersion;

        return $this;
    }

    /**
     * Get Formulario entity (many to one).
     *
     * @return \App\Entity\FormularioVersion
     */
    public function getFormularioVersion()
    {
        return $this->formularioVersion;
    }

    /**
     * Set the value of opcion_formulario_id.
     *
     * @param integer $permite_id
     * @return \App\Entity\OpcionFormularioVersion
     */
    public function setOpcionFormularioId($opcion_formulario_id)
    {
        $this->opcion_formulario_id = $opcion_formulario_id;

        return $this;
    }

    /**
     * Get the value of permite_id.
     *
     * @return integer
     */
    public function getOpcionFormularioId()
    {
        return $this->opcion_formulario_id;
    }

    /**
     * Set OpcionFormulario entity (many to one).
     *
     * @param \App\Entity\OpcionFormulario $opcionFormulario
     * @return \App\Entity\OpcionFormularioVersion
     */
    public function setOpcionFormulario(OpcionFormulario $opcionFormulario = null)
    {
        $this->opcionFormulario = $opcionFormulario;

        return $this;
    }

    /**
     * Get OpcionFormulario entity (many to one).
     *
     * @return \App\Entity\OpcionFormulario
     */
    public function getOpcionFormulario()
    {
        return $this->opcionFormulario;
    }

    public function __sleep()
    {
        return array('id', 'formulario_version_id', 'opcion_formulario_id');
    }
}
