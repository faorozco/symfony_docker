<?php



namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\PasoCampoVersion
 *
 * @ORM\Entity()
 * @ORM\Table(name="paso_campo_version", indexes={@ORM\Index(name="fk_paso_campo_version_campo_formulario_version1_idx", columns={"campo_formulario_version_id"}), @ORM\Index(name="fk_paso_campo_version_paso1_idx", columns={"paso_version_id"})})
 */
class PasoCampoVersion
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
    protected $campo_formulario_version_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $paso_campo_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $paso_version_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="CampoFormularioVersion", inversedBy="pasoCamposVersion")
     * @ORM\JoinColumn(name="campo_formulario_version_id", referencedColumnName="id", nullable=false)
     */
    protected $campoFormularioVersion;


    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\PasoCampoVersion
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
     * Set the value of campo_formulario_version_id.
     *
     * @param integer $campo_formulario_version_id
     * @return \App\Entity\PasoCampoVersion
     */
    public function setCampoFormularioVersionId($campo_formulario_version_id)
    {
        $this->campo_formulario_version_id = $campo_formulario_version_id;

        return $this;
    }

    /**
     * Get the value of campo_formulario_version_id.
     *
     * @return integer
     */
    public function getCampoFormularioVersionId()
    {
        return $this->campo_formulario_version_id;
    }

    /**
     * Set the value of paso_campo_id.
     *
     * @param integer $paso_campo_id
     * @return \App\Entity\PasoCampoVersion
     */
    public function setPasoCampoId($paso_campo_id)
    {
        $this->paso_campo_id = $paso_campo_id;

        return $this;
    }

    /**
     * Get the value of paso_campo_id.
     *
     * @return integer
     */
    public function getPasoCampoId()
    {
        return $this->paso_campo_id;
    }

    /**
     * Set the value of paso_version_id.
     *
     * @param integer $paso_version_id
     * @return \App\Entity\PasoVersion
     */
    public function setPasoVersionId($paso_version_id)
    {
        $this->paso_version_id = $paso_version_id;

        return $this;
    }

    /**
     * Get the value of paso_version_id.
     *
     * @return integer
     */
    public function getPasoVersionId()
    {
        return $this->paso_version_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\PasoCampoVersion
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
     * Set CampoFormularioVersion entity (many to one).
     *
     * @param \App\Entity\CampoFormularioVersion $campoFormularioVersion
     * @return \App\Entity\PasoCampoVersion
     */
    public function setCampoFormularioVersion(CampoFormularioVersion $campoFormularioVersion = null)
    {
        $this->campoFormularioVersion = $campoFormularioVersion;

        return $this;
    }

    /**
     * Get CampoFormularioVersion entity (many to one).
     *
     * @return \App\Entity\CampoFormularioVersion
     */
    public function getCampoFormularioVersion()
    {
        return $this->campoFormularioVersion;
    }

    /**
     * Get Paso entity (many to one).
     *
     * @return \App\Entity\Paso
     */
    public function getPaso()
    {
        return $this->paso;
    }

    public function __sleep()
    {
        return array('id', 'campo_formulario_version_id', 'paso_campo_id', 'paso_version_id', 'estado_id');
    }
}