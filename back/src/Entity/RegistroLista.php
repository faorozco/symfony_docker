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
 *         "path"="/registro_listas/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * App\Entity\RegistroLista
 *
 * @ORM\Entity()
 * @ORM\Table(name="registro_lista", indexes={@ORM\Index(name="fk_Registro_numerico_Registro1_idx", columns={"registro_id"}), @ORM\Index(name="fk_Registro_numerico_campo_formulario_version1_idx", columns={"campo_formulario_version_id"}), @ORM\Index(name="fk_registro_lista_detalle_lista1_idx", columns={"detalle_lista_id"})})
 */
class RegistroLista
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
    protected $id_lista;

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
    protected $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $detalle_lista_id;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="registroListas")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    /**
     * @ORM\ManyToOne(targetEntity="CampoFormularioVersion", inversedBy="registroListas")
     * @ORM\JoinColumn(name="campo_formulario_version_id", referencedColumnName="id", nullable=false)
     */
    protected $campoFormularioVersion;

    /**
     * @ORM\ManyToOne(targetEntity="DetalleLista", inversedBy="registroListas")
     * @ORM\JoinColumn(name="detalle_lista_id", referencedColumnName="id", nullable=false)
     */
    protected $detalleLista;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\RegistroLista
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
     * Set the value of id_lista.
     *
     * @param integer $id_lista
     * @return \App\Entity\RegistroLista
     */
    public function setIdLista($id_lista)
    {
        $this->id_lista = $id_lista;

        return $this;
    }

    /**
     * Get the value of id_lista.
     *
     * @return integer
     */
    public function getIdLista()
    {
        return $this->id_lista;
    }

    /**
     * Set the value of valor.
     *
     * @param string $valor
     * @return \App\Entity\RegistroLista
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
     * @return \App\Entity\RegistroLista
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
     * @return \App\Entity\RegistroLista
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\RegistroLista
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
     * Set the value of detalle_lista_id.
     *
     * @param integer $detalle_lista_id
     * @return \App\Entity\RegistroLista
     */
    public function setDetalleListaId($detalle_lista_id)
    {
        $this->detalle_lista_id = $detalle_lista_id;

        return $this;
    }

    /**
     * Get the value of detalle_lista_id.
     *
     * @return integer
     */
    public function getDetalleListaId()
    {
        return $this->detalle_lista_id;
    }

    /**
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\RegistroLista
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
     * @return \App\Entity\RegistroLista
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
     * Set DetalleLista entity (many to one).
     *
     * @param \App\Entity\DetalleLista $detalleLista
     * @return \App\Entity\RegistroLista
     */
    public function setDetalleLista(DetalleLista $detalleLista = null)
    {
        $this->detalleLista = $detalleLista;

        return $this;
    }

    /**
     * Get DetalleLista entity (many to one).
     *
     * @return \App\Entity\DetalleLista
     */
    public function getDetalleLista()
    {
        return $this->detalleLista;
    }

    public function __sleep()
    {
        return array('id', 'id_lista', 'valor', 'registro_id', 'campo_formulario_version_id', 'estado_id', 'detalle_lista_id');
    }
}
