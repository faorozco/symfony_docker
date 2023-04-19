<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\SedeSave;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "save"={
 *          "method"="POST",
 *          "path"="/sedes",
 *          "controller"=SedeSave::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/sedes/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"codigo_interno","nombre","direccion","pbx","celular","email"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"codigo_interno", "nombre","direccion","pbx","celular","email"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Sede
 *
 * @ORM\Entity()
 * @ORM\Table(name="sede", indexes={@ORM\Index(name="fk_sede_empresa1_idx", columns={"empresa_id"})})
 */
class Sede
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $codigo_interno;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $direccion;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected $pbx;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected $celular;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $empresa_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="Proceso", mappedBy="sede")
     * @ORM\JoinColumn(name="id", referencedColumnName="sede_id", nullable=false)
     */
    protected $procesos;

    /**
     * @ORM\ManyToOne(targetEntity="Empresa", inversedBy="sedes")
     * @ORM\JoinColumn(name="empresa_id", referencedColumnName="id", nullable=false)
     */
    protected $empresa;

    /**
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="sede")
     * @ORM\JoinColumn(name="id", referencedColumnName="sede_id", nullable=false)
     */
    protected $usuarios;

    public function __construct()
    {
        $this->procesos = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Sede
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
     * Set the value of codigo_interno.
     *
     * @param string $codigo_interno
     * @return \App\Entity\Sede
     */
    public function setCodigoInterno($codigo_interno)
    {
        $this->codigo_interno = $codigo_interno;

        return $this;
    }

    /**
     * Get the value of codigo_interno.
     *
     * @return string
     */
    public function getCodigoInterno()
    {
        return $this->codigo_interno;
    }

    /**
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\Sede
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of direccion.
     *
     * @param string $direccion
     * @return \App\Entity\Sede
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get the value of direccion.
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set the value of pbx.
     *
     * @param string $pbx
     * @return \App\Entity\Sede
     */
    public function setPbx($pbx)
    {
        $this->pbx = $pbx;

        return $this;
    }

    /**
     * Get the value of pbx.
     *
     * @return string
     */
    public function getPbx()
    {
        return $this->pbx;
    }

    /**
     * Set the value of celular.
     *
     * @param integer $celular
     * @return \App\Entity\Sede
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get the value of celular.
     *
     * @return integer
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return \App\Entity\Sede
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of url.
     *
     * @param string $url
     * @return \App\Entity\Sede
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of empresa_id.
     *
     * @param integer $empresa_id
     * @return \App\Entity\Sede
     */
    public function setEmpresaId($empresa_id)
    {
        $this->empresa_id = $empresa_id;

        return $this;
    }

    /**
     * Get the value of empresa_id.
     *
     * @return integer
     */
    public function getEmpresaId()
    {
        return $this->empresa_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Sede
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
     * Add Proceso entity to collection (one to many).
     *
     * @param \App\Entity\Proceso $proceso
     * @return \App\Entity\Sede
     */
    public function addProceso(Proceso $proceso)
    {
        $this->procesos[] = $proceso;

        return $this;
    }

    /**
     * Remove Proceso entity from collection (one to many).
     *
     * @param \App\Entity\Proceso $proceso
     * @return \App\Entity\Sede
     */
    public function removeProceso(Proceso $proceso)
    {
        $this->procesos->removeElement($proceso);

        return $this;
    }

    /**
     * Get Proceso entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProcesos()
    {
        return $this->procesos;
    }

    /**
     * Set Empresa entity (many to one).
     *
     * @param \App\Entity\Empresa $empresa
     * @return \App\Entity\Sede
     */
    public function setEmpresa(Empresa $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get Empresa entity (many to one).
     *
     * @return \App\Entity\Empresa
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

/**
 * Add Usuario entity to collection (one to many).
 *
 * @param \App\Entity\Usuario $usuario
 * @return \App\Entity\Sede
 */
    public function addUsuario(Usuario $usuario)
    {
        $this->usuarios[] = $usuario;

        return $this;
    }

    /**
     * Remove Usuario entity from collection (one to many).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Sede
     */
    public function removeUsuario(Usuario $usuario)
    {
        $this->usuarios->removeElement($usuario);

        return $this;
    }

    /**
     * Get Usuario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    public function __sleep()
    {
        return array('id', 'codigo_interno', 'nombre', 'direccion', 'pbx', 'celular', 'email', 'url', 'empresa_id', 'estado_id');
    }
}
