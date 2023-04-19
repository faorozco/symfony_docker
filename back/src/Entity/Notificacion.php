<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\ProcessPreselected;
use App\Controller\SaveNotificacion;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
 *         "controller"=SaveNotificacion::class
 *        },
 *      "processpreselected"={
 *          "method"="POST",
 *          "path"="/notificacions/preselected/process",
 *          "controller"=ProcessPreselected::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/notificacions/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * App\Entity\Notificacion
 *
 * @ORM\Entity(repositoryClass="App\Repository\NotificacionRepository")
 * @ORM\Table(name="notificacion", indexes={@ORM\Index(name="fk_Registro_numerico_Registro1_idx", columns={"registro_id"})})
 */
class Notificacion
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
    protected $contenido;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registro_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $para;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $notificado;

    /**
     * @ORM\OneToMany(targetEntity="Notificado", mappedBy="notificacion")
     * @ORM\JoinColumn(name="id", referencedColumnName="notificacion_id", nullable=false)
     */
    protected $notificados;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="notificacions")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    public function __construct()
    {
        $this->notificados = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Notificacion
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
     * @return \App\Entity\Notificacion
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
     * Set the value of contenido.
     *
     * @param string $contenido
     * @return \App\Entity\Notificacion
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
     * Set the value of registro_id.
     *
     * @param integer $registro_id
     * @return \App\Entity\Notificacion
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
     * @return \App\Entity\Notificacion
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
     * Set the value of para.
     *
     * @param string $para
     * @return \App\Entity\Notificacion
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
     * Set the value of notificado.
     *
     * @param boolean $notificado
     * @return \App\Entity\Notificacion
     */
    public function setNotificado($notificado)
    {
        $this->notificado = $notificado;

        return $this;
    }

    /**
     * Get the value of notificado.
     *
     * @return boolean
     */
    public function getNotificado()
    {
        return $this->notificado;
    }

    /**
     * Add Notificado entity to collection (one to many).
     *
     * @param \App\Entity\Notificado $notificado
     * @return \App\Entity\Notificacion
     */
    public function addNotificado(Notificado $notificado)
    {
        $this->notificados[] = $notificado;

        return $this;
    }

    /**
     * Remove Notificado entity from collection (one to many).
     *
     * @param \App\Entity\Notificado $notificado
     * @return \App\Entity\Notificacion
     */
    public function removeNotificado(Notificado $notificado)
    {
        $this->notificados->removeElement($notificado);

        return $this;
    }

    /**
     * Get Notificado entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificados()
    {
        return $this->notificados;
    }

    /**
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Notificacion
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
        return array('id', 'cuando', 'contenido', 'registro_id', 'estado_id', 'para', 'notificado');
    }
}
