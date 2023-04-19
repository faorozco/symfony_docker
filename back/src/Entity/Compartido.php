<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\SaveCompartido;
use App\Controller\Async\SaveSharedAsync;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
 *          "controller"=SaveCompartido::class
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/compartidos/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *  "postMessages"={
 *         "method"="POST",
 *         "path"="/compartidos/save/async",
 *          "controller"=SaveSharedAsync::class,
 *          "validate"=false,
 *           "defaults"={"_api_receive"=false}
 *        },
 *  }
 * )
 * App\Entity\Compartido
 *
 * @ORM\Entity()
 * @ORM\Table(name="compartido", indexes={@ORM\Index(name="fk_Registro_numerico_Registro1_idx", columns={"registro_id"})})
 */
class Compartido
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $cuando;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $para;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $asunto;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $contenido;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $tipo_notificacion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $descripcion_adjuntos;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registro_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="Enviado", mappedBy="compartido")
     * @ORM\JoinColumn(name="id", referencedColumnName="compartido_id", nullable=false)
     */
    protected $enviados;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="compartidos")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    public function __construct()
    {
        $this->enviados = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Compartido
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
     * Set the value of cuando.
     *
     * @param \DateTime $cuando
     * @return \App\Entity\Compartido
     */
    public function setCuando($cuando)
    {
        $this->cuando = $cuando;

        return $this;
    }

    /**
     * Get the value of cuando.
     *
     * @return \DateTime
     */
    public function getCuando()
    {
        return $this->cuando;
    }

    /**
     * Set the value of para.
     *
     * @param string $para
     * @return \App\Entity\Compartido
     */
    public function setPara($para)
    {
        $this->para = $para;

        return $this;
    }

    /**
     * Get the value of para.
     *
     * @return string
     */
    public function getPara()
    {
        return $this->para;
    }

    /**
     * Set the value of asunto.
     *
     * @param string $asunto
     * @return \App\Entity\Compartido
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;

        return $this;
    }

    /**
     * Get the value of asunto.
     *
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * Set the value of contenido.
     *
     * @param string $contenido
     * @return \App\Entity\Compartido
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
     * Set the value of tipo_notificacion.
     *
     * @param string $tipo_notificacion
     * @return \App\Entity\Compartido
     */
    public function setTipoNotificacion($tipo_notificacion)
    {
        $this->tipo_notificacion = $tipo_notificacion;

        return $this;
    }

    /**
     * Get the value of tipo_notificacion.
     *
     * @return string
     */
    public function getTipoNotificacion()
    {
        return $this->tipo_notificacion;
    }

    /**
     * Set the value of descripcion_adjuntos.
     *
     * @param string $descripcion_adjuntos
     * @return \App\Entity\Compartido
     */
    public function setDescripcionAdjuntos($descripcion_adjuntos)
    {
        $this->descripcion_adjuntos = $descripcion_adjuntos;

        return $this;
    }

    /**
     * Get the value of descripcion_adjuntos.
     *
     * @return string
     */
    public function getDescripcionAdjuntos()
    {
        return $this->descripcion_adjuntos;
    }

    /**
     * Set the value of registro_id.
     *
     * @param integer $registro_id
     * @return \App\Entity\Compartido
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Compartido
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
     * Add Enviado entity to collection (one to many).
     *
     * @param \App\Entity\Enviado $enviado
     * @return \App\Entity\Compartido
     */
    public function addEnviado(Enviado $enviado)
    {
        $this->enviados[] = $enviado;

        return $this;
    }

    /**
     * Remove Enviado entity from collection (one to many).
     *
     * @param \App\Entity\Enviado $enviado
     * @return \App\Entity\Compartido
     */
    public function removeEnviado(Enviado $enviado)
    {
        $this->enviados->removeElement($enviado);

        return $this;
    }

    /**
     * Get Enviado entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnviados()
    {
        return $this->enviados;
    }

    /**
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Compartido
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

    public function __sleep()
    {
        return array('id', 'cuando', 'para', 'asunto', 'contenido', 'descripcion_adjuntos', 'registro_id', 'estado_id');
    }
}
