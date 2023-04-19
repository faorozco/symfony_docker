<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\SaveComment;

/**
 * @ApiResource(
 *   collectionOperations={
 *      "post"={
 *         "controller"=SaveComment::class,
 *         "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 *  @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"detalle","fecha"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"detalle","fecha"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Comentario
 *
 * @ORM\Entity()
 * @ORM\Table(name="comentario", indexes={@ORM\Index(name="fk_comentario_registro_idx", columns={"registro_id"}), @ORM\Index(name="fk_comentario_usuario_idx", columns={"usuario_id"})})
 */
class Comentario
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    protected $detalle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $usuario_id;

    /**
     * @ORM\Column(type="date")
     */
    protected $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="comentarios")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $usuario;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registro_id;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="comentarios")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Comentario
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
     * Set the value of detalle.
     *
     * @param string $detalle
     * @return \App\Entity\Comentario
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get the value of detalle.
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Set the value of usuario_id.
     *
     * @param integer $usuario_id
     * @return \App\Entity\Comentario
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
     * Set Usuario entity (many to one).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Comentario
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

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Comentario
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
     * Set the value of registro_id.
     *
     * @param integer $registro_id
     * @return \App\Entity\Archivo
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
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Comentario
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
     * Set the value of fecha
     *
     * @param \DateTime $fecha
     * @return \App\Entity\Archivo
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get the value of fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    public function __sleep()
    {
        return array('id', 'detalle', 'fecha', 'estado_id', 'usuario_id', 'registro_id');
    }
}
