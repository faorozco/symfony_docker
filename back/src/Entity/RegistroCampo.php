<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
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
 *         "path"="/registro_campos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * App\Entity\RegistroCampo
 *
 *  @ORM\Entity(repositoryClass="App\Repository\RegistroCampoRepository")
 * @ORM\Table(name="registro_campo", indexes={@ORM\Index(name="fk_Registro_numerico_Registro1_idx", columns={"registro_id"}), @ORM\Index(name="fk_Registro_numerico_campo_formulario_version1_idx", columns={"campo_formulario_version_id"})})
 */
class RegistroCampo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id_campo;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $valor;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registro_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $campo_formulario_version_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $registro_id_origen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="registroCampos")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    /**
     * @ORM\ManyToOne(targetEntity="CampoFormularioVersion", inversedBy="registroCampos")
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
     * @return \App\Entity\RegistroCampo
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
     * Set the value of id_campo.
     *
     * @param integer $id_campo
     * @return \App\Entity\RegistroCampo
     */
    public function setIdCampo($id_campo)
    {
        $this->id_campo = $id_campo;

        return $this;
    }

    /**
     * Get the value of id_campo.
     *
     * @return integer
     */
    public function getIdCampo()
    {
        return $this->id_campo;
    }

    /**
     * Set the value of valor.
     *
     * @param string $valor
     * @return \App\Entity\RegistroCampo
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get the value of valor.
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of registro_id.
     *
     * @param integer $registro_id
     * @return \App\Entity\RegistroCampo
     */
    public function setRegistroId($registro_id)
    {
        $this->registro_id = $registro_id;

        return $this;
    }

    /**
     * Get the value of registro_id.
     *
     * @return integer
     */
    public function getRegistroId()
    {
        return $this->registro_id;
    }

    /**
     * Set the value of campo_formulario_version_id.
     *
     * @param integer $campo_formulario_version_id
     * @return \App\Entity\RegistroCampo
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
     * Set the value of registro_id_origen.
     *
     * @param integer $registro_id_origen
     * @return \App\Entity\RegistroCampo
     */
    public function setRegistroIdOrigen($registro_id_origen)
    {
        $this->registro_id_origen = $registro_id_origen;

        return $this;
    }

    /**
     * Get the value of registro_id_origen.
     *
     * @return integer
     */
    public function getRegistroIdOrigen()
    {
        return $this->registro_id_origen;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\RegistroCampo
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
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\RegistroCampo
     */
    public function setRegistro(Registro $registro = null)
    {
        $this->registro = $registro;

        return $this;
    }

    /**
     * Get Registro entity (many to one).
     *
     * @return \App\Entity\Registro
     */
    public function getRegistro()
    {
        return $this->registro;
    }

    /**
     * Set CampoFormularioVersion entity (many to one).
     *
     * @param \App\Entity\CampoFormularioVersion $campoFormularioVersion
     * @return \App\Entity\RegistroCampo
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

    public function __sleep()
    {
        return array('id', 'id_campo', 'valor', 'registro_id', 'campo_formulario_version_id', 'registro_id_origen', 'estado_id');
    }
}
