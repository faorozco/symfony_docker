<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

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
 *         "path"="/enviados/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * App\Entity\Enviado
 *
 * @ORM\Entity(repositoryClass="App\Repository\EnviadoRepository")
 * @ORM\Table(name="enviado", indexes={@ORM\Index(name="fk_enviado_compartido1_idx", columns={"compartido_id"})})
 */
class Enviado
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
    protected $destinatario;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_enviado;

    /**
     * @ORM\Column(type="integer")
     */
    protected $compartido_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="Compartido", inversedBy="enviados")
     * @ORM\JoinColumn(name="compartido_id", referencedColumnName="id", nullable=false)
     */
    protected $compartido;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Enviado
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
     * Set the value of destinatario.
     *
     * @param string $destinatario
     * @return \App\Entity\Enviado
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;

        return $this;
    }

    /**
     * Get the value of destinatario.
     *
     * @return string
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }

    /**
     * Set the value of fecha_enviado.
     *
     * @param \DateTime $fecha_enviado
     * @return \App\Entity\Enviado
     */
    public function setFechaEnviado($fecha_enviado)
    {
        $this->fecha_enviado = $fecha_enviado;

        return $this;
    }

    /**
     * Get the value of fecha_enviado.
     *
     * @return \DateTime
     */
    public function getFechaEnviado()
    {
        return $this->fecha_enviado;
    }

    /**
     * Set the value of compartido_id.
     *
     * @param integer $compartido_id
     * @return \App\Entity\Enviado
     */
    public function setCompartidoId($compartido_id)
    {
        $this->compartido_id = $compartido_id;

        return $this;
    }

    /**
     * Get the value of compartido_id.
     *
     * @return integer
     */
    public function getCompartidoId()
    {
        return $this->compartido_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Enviado
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
     * Set Compartido entity (many to one).
     *
     * @param \App\Entity\Compartido $compartido
     * @return \App\Entity\Enviado
     */
    public function setCompartido(Compartido $compartido = null)
    {
        $this->compartido = $compartido;

        return $this;
    }

    /**
     * Get Compartido entity (many to one).
     *
     * @return \App\Entity\Compartido
     */
    public function getCompartido()
    {
        return $this->compartido;
    }

    public function __sleep()
    {
        return array('id', 'destinatario', 'fecha_enviado', 'compartido_id', 'estado_id');
    }
}