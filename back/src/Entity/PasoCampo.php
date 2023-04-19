<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\PasoCampo
 *
 * @ORM\Entity()
 * @ORM\Table(name="paso_campo", indexes={@ORM\Index(name="fk_paso_campo_campo_formulario1_idx", columns={"campo_formulario_id"}), @ORM\Index(name="fk_paso_campo_paso1_idx", columns={"paso_id"})})
 */
class PasoCampo
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
    protected $campo_formulario_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $paso_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="CampoFormulario", inversedBy="pasoCampos")
     * @ORM\JoinColumn(name="campo_formulario_id", referencedColumnName="id", nullable=false)
     */
    protected $campoFormulario;


    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\PasoCampo
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
     * Set the value of campo_formulario_id.
     *
     * @param integer $campo_formulario_id
     * @return \App\Entity\PasoCampo
     */
    public function setCampoFormularioId($campo_formulario_id)
    {
        $this->campo_formulario_id = $campo_formulario_id;

        return $this;
    }

    /**
     * Get the value of campo_formulario_id.
     *
     * @return integer
     */
    public function getCampoFormularioId()
    {
        return $this->campo_formulario_id;
    }

    /**
     * Set the value of paso_id.
     *
     * @param integer $paso_id
     * @return \App\Entity\PasoCampo
     */
    public function setPasoId($paso_id)
    {
        $this->paso_id = $paso_id;

        return $this;
    }

    /**
     * Get the value of paso_id.
     *
     * @return integer
     */
    public function getPasoId()
    {
        return $this->paso_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\PasoCampo
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
     * Set CampoFormulario entity (many to one).
     *
     * @param \App\Entity\CampoFormulario $campoFormulario
     * @return \App\Entity\PasoCampo
     */
    public function setCampoFormulario(CampoFormulario $campoFormulario = null)
    {
        $this->campoFormulario = $campoFormulario;

        return $this;
    }

    /**
     * Get CampoFormulario entity (many to one).
     *
     * @return \App\Entity\CampoFormulario
     */
    public function getCampoFormulario()
    {
        return $this->campoFormulario;
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
        return array('id', 'campo_formulario_id', 'paso_id', 'estado_id');
    }
}