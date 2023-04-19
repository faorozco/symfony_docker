<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\ORSearchFilter;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\ListarAuditoria;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={
 *          "method"="GET",
 *          "path"="/auditorias",
 *          "controller"=ListarAuditoria::class,
 *          "defaults"={
 *              "_items_per_page"=10
 *       }
 *       },
 *       "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/auditorias/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"valor_anterior","valor_actual", "operacion"}
 * )
 * 
 * @ApiFilter(DateFilter::class, properties={"fecha"})
 * 
 * @ApiFilter(
 *  SearchFilter::class,
 *      properties={
 *          "estado_id": "exact",
 *          "entidad": "exact",
 *          "usuario": "exact",
 *          "entidad_id": "exact"
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\AuditoriaRepository")
 * App\Entity\Auditoria
 * @ORM\Table(name="auditoria", indexes={@ORM\Index(name="fk_auditoria_usuario1_idx", columns={"usuario_id"})})
 */
class Auditoria
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $entidad;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $entidad_id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $operacion;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $valor_anterior;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $valor_actual;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $ip_cliente;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="fecha")
     */
    protected $fecha;

    /**
     * @ORM\Column(type="integer")
     */
    protected $usuario_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="auditorias")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable=false)
     */
    protected $usuario;

    /**
     * @ORM\Column(type="string", length=90)
     */
    private $username;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Auditoria
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
     * Set the value of entidad.
     *
     * @param string $entidad
     * @return \App\Entity\Auditoria
     */
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get the value of entidad.
     *
     * @return string
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set the value of entidad_id.
     *
     * @param integer $entidad_id
     * @return \App\Entity\Auditoria
     */
    public function setEntidadId($entidad_id)
    {
        $this->entidad_id = $entidad_id;

        return $this;
    }

    /**
     * Get the value of entidad_id.
     *
     * @return integer
     */
    public function getEntidadId()
    {
        return $this->entidad_id;
    }

    /**
     * Set the value of operacion.
     *
     * @param string $operacion
     * @return \App\Entity\Auditoria
     */
    public function setOperacion($operacion)
    {
        $this->operacion = $operacion;

        return $this;
    }

    /**
     * Get the value of operacion.
     *
     * @return string
     */
    public function getOperacion()
    {
        return $this->operacion;
    }

    /**
     * Set the value of valor_anterior.
     *
     * @param string $valor_anterior
     * @return \App\Entity\Auditoria
     */
    public function setValorAnterior($valor_anterior)
    {
        $this->valor_anterior = $valor_anterior;

        return $this;
    }

    /**
     * Get the value of valor_anterior.
     *
     * @return string
     */
    public function getValorAnterior()
    {
        return $this->valor_anterior;
    }

    /**
     * Set the value of valor_actual.
     *
     * @param string $valor_actual
     * @return \App\Entity\Auditoria
     */
    public function setValorActual($valor_actual)
    {
        $this->valor_actual = $valor_actual;

        return $this;
    }

    /**
     * Get the value of valor_actual.
     *
     * @return string
     */
    public function getValorActual()
    {
        return $this->valor_actual;
    }

    /**
     * Set the value of ip_cliente.
     *
     * @param string $ip_cliente
     * @return \App\Entity\Auditoria
     */
    public function setIpCliente($ip_cliente)
    {
        $this->ip_cliente = $ip_cliente;

        return $this;
    }

    /**
     * Get the value of ip_cliente.
     *
     * @return string
     */
    public function getIpCliente()
    {
        return $this->ip_cliente;
    }

    /**
     * Set the value of usuario_id.
     *
     * @param integer $usuario_id
     * @return \App\Entity\Auditoria
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
     * @return \App\Entity\Auditoria
     */
    public function setUsuario($usuario = null)
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
     * @return \App\Entity\Auditoria
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
     * Get the value of fecha
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set the value of fecha
     *
     * @return  self
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function __sleep()
    {
        return array('id', 'entidad', 'entidad_id', 'operacion', 'valor_anterior', 'valor_actual', 'ip_cliente', 'fecha', 'usuario_id', 'estado_id');
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
