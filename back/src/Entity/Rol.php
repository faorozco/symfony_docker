<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Componente;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *   collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *   },
 *   itemOperations={
 *       "put",
 *       "get"={
 *         "method"="GET",
 *         "path"="/rols/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *   }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\Rol
 *
 * @ORM\Entity()
 * @ORM\Table(name="rol")
 */
class Rol
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
    protected $nombre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToMany(targetEntity="Componente", inversedBy="rols")
     * @ORM\JoinTable(
     *  name="rol_componente",
     *  joinColumns={
     *      @ORM\JoinColumn(name="rol_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="componente_id", referencedColumnName="id")
     *  }
     * )
     * @ApiSubresource
     */
    protected $componentes;

    /**
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="rols")
     * @ApiSubresource(maxDepth=1)
     */
    protected $usuarios;


    /**
     * @ORM\Column(type="boolean")
     */
    private $Creater;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Reader;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Updated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Deleter;


    public function __construct()
    {
        $this->componentes = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Rol
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
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\Rol
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Rol
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
     * Add Componente entity to collection (one to many).
     *
     * @param \App\Entity\Componente $componente
     * @return \App\Entity\Rol
     */
    public function addComponente(Componente $componente)
    {
        $this->componentes[] = $componente;

        return $this;
    }

    /**
     * Remove Componente entity from collection (one to many).
     *
     * @param \App\Entity\Componente $componente
     * @return \App\Entity\Rol
     */
    public function removeComponente(Componente $componente)
    {
        $this->componentes->removeElement($componente);

        return $this;
    }

    /**
     * Get Componente entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponentes()
    {
        return $this->componentes;
    }

    /**
     * Add Usuario entity to collection (one to many).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Rol
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
     * @return \App\Entity\Rol
     */
    public function removeUsuario(Usuario $usuario)
    {
        $this->usuarios->removeElement($usuario);

        return $this;
    }

    /**
     * Get UsuarioRol entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }


    public function __sleep()
    {
        return array('id', 'nombre', 'estado_id');
    }

    public function getCreater(): ?bool
    {
        return $this->Creater;
    }

    public function setCreater(bool $Creater): self
    {
        $this->Creater = $Creater;

        return $this;
    }

    public function getReader(): ?bool
    {
        return $this->Reader;
    }

    public function setReader(bool $Reader): self
    {
        $this->Reader = $Reader;

        return $this;
    }

    public function getUpdated(): ?bool
    {
        return $this->Updated;
    }

    public function setUpdated(bool $Updated): self
    {
        $this->Updated = $Updated;

        return $this;
    }

    public function getDeleter(): ?bool
    {
        return $this->Deleter;
    }

    public function setDeleter(bool $Deleter): self
    {
        $this->Deleter = $Deleter;

        return $this;
    }
}
