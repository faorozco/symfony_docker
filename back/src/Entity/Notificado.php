<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\NotifiedNotificacions;
use App\Controller\NotifiedSeen;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "getEspecial"={
 *          "method"="GET",
 *          "path"="/notificados/user",
 *          "controller"=NotifiedNotificacions::class,
 *          "defaults"={
 *              "_items_per_page"=10,
 *              "_estado_id"=1
 *          }
 *      },
 *      "notifiedSeen"={
 *          "method"="POST",
 *          "path"="/notificados/{id}/seen",
 *          "controller"=NotifiedSeen::class
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/notificados/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * App\Entity\Notificado
 *
 * @ORM\Entity(repositoryClass="App\Repository\NotificadoRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="notificado", indexes={@ORM\Index(name="fk_notificados_notificacion1_idx", columns={"notificacion_id"}), @ORM\Index(name="fk_notificados_usuario1_idx", columns={"usuario_id"})})
 */
class Notificado
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
    protected $visto;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $enviado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $comentario;

    /**
     * @ORM\Column(type="integer")
     */
    protected $notificacion_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $usuario_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="Notificacion", inversedBy="notificados")
     * @ORM\JoinColumn(name="notificacion_id", referencedColumnName="id", nullable=false)
     */
    protected $notificacion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="notificados")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     */
    protected $usuario;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Notificado
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
     * Set the value of visto.
     *
     * @param \DateTime $visto
     * @return \App\Entity\Notificado
     */
    public function setVisto($visto)
    {
        $this->visto = $visto;

        return $this;
    }

    /**
     * Get the value of visto.
     *
     * @return \DateTime
     */
    public function getVisto()
    {
        return $this->visto;
    }

    /**
     * Get the value of enviado
     */
    public function getEnviado()
    {
        return $this->enviado;
    }

    /**
     *
     * Set the value of enviado
     *
     * @return  self
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;

        return $this;
    }

    /**
     * Set the value of notificacion_id.
     *
     * @param integer $notificacion_id
     * @return \App\Entity\Notificado
     */
    public function setNotificacionId($notificacion_id)
    {
        $this->notificacion_id = $notificacion_id;

        return $this;
    }

    /**
     * Get the value of notificacion_id.
     *
     * @return integer
     */
    public function getNotificacionId()
    {
        return $this->notificacion_id;
    }

    /**
     * Set the value of usuario_id.
     *
     * @param integer $usuario_id
     * @return \App\Entity\Notificado
     */
    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;

        return $this;
    }

    /**
     * Get the value of usuario_id.
     *
     * @return integer
     */
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Notificado
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
     * Set the value of comentario.
     *
     * @param boolean $comentario
     * @return \App\Entity\Notificado
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get the value of comentario.
     *
     * @return boolean
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set Notificacion entity (many to one).
     *
     * @param \App\Entity\Notificacion $notificacion
     * @return \App\Entity\Notificado
     */
    public function setNotificacion(Notificacion $notificacion = null)
    {
        $this->notificacion = $notificacion;

        return $this;
    }

    /**
     * Get Notificacion entity (many to one).
     *
     * @return \App\Entity\Notificacion
     */
    public function getNotificacion()
    {
        return $this->notificacion;
    }

    /**
     * Set Usuario entity (many to one).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Notificado
     */
    public function setUsuario(Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get Usuario entity (many to one).
     *
     * @return \App\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    public function __sleep()
    {
        return array('id', 'visto', 'enviado', 'notificacion_id', 'usuario_id', 'estado_id');
    }

    /**
     * @ORM\PrePersist
     */
    public function setDefaultEnviado()
    {
        $this->setEnviado(new \DateTime());
    }
}
