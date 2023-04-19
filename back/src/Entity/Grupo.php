<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\Grupo\GroupGetAll;
use App\Controller\Grupo\GroupUpdateSpecial;
use App\Controller\Grupo\GroupCreate;
use App\Controller\Grupo\GroupOnlyGet;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={        
 *         "method"="GET",
 *         "path"="/grupos",
 *         "controller"=GroupGetAll::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *      "post"={
 *         "method"="POST",
 *         "path"="/grupos",
 *         "controller"=GroupCreate::class,
 *         "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *     "update"={
 *         "method"="PUT",
 *         "path"="/grupos/{id}/update",
 *         "controller"=GroupUpdateSpecial::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *      "get"={
 *         "method"="GET",
 *         "path"="/grupos/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "get"={        
 *         "method"="GET",
 *         "path"="/grupos/users/{id}",
 *         "controller"=GroupOnlyGet::class,
 *         "requirements"={"id"="\d+"}
 *          }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"id", "nombre"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Grupo
 * @ORM\Entity(repositoryClass="App\Repository\GrupoRepository")
 * @ORM\Table(name="grupo")
 */
class Grupo
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
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $modo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\ManyToMany(targetEntity="Formulario", mappedBy="grupos"))
     * @ApiSubresource(maxDepth=1)
     */
    protected $formularios;

    /**
     * @ORM\ManyToMany(targetEntity="ConsultaMaestra", mappedBy="grupos")
     * @ApiSubresource(maxDepth=1)
     */
    protected $consultaMaestras;


    /**
     * @ORM\ManyToMany(targetEntity="Usuario", inversedBy="grupos")
     * @ORM\JoinTable(name="usuario_grupo",
     *     joinColumns={@ORM\JoinColumn(name="grupo_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
     * )
     */
    protected $usuarios;

    public function __construct()
    {
        $this->formularios = new ArrayCollection();
        $this->consultaMaestras = new ArrayCollection();
        $this->consultasMaestrasVersion = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Grupo
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
     * @return \App\Entity\Grupo
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
     * Set the value of modo.
     *
     * @param string $modo
     * @return \App\Entity\Grupo
     */
    public function setModo($modo)
    {
        $this->modo = $modo;

        return $this;
    }

    /**
     * Get the value of modo.
     *
     * @return string
     */
    public function getModo()
    {
        return $this->modo;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Grupo
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
     * Add Formulario entity to collection (one to many).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\Grupo
     */
    public function addFormulario(Formulario $formulario)
    {
        $this->formularios[] = $formulario;

        return $this;
    }

    /**
     * Remove Formulario entity from collection (one to many).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\Grupo
     */
    public function removeFormulario(Formulario $formulario)
    {
        $this->formularios->removeElement($formulario);

        return $this;
    }

    /**
     * Get Formulario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getformularios()
    {
        return $this->formularios;
    }

    /**
     * Add ConsultaMaestra entity to collection.
     *
     * @param \App\Entity\ConsultaMaestra $consultaMaestra
     * @return \App\Entity\Grupo
     */
    public function addConsultaMaestra(ConsultaMaestra $consultaMaestra)
    {
        $this->consultaMaestras[] = $consultaMaestra;

        return $this;
    }

    /**
     * Remove ConsultaMaestra entity from collection.
     *
     * @param \App\Entity\ConsultaMaestra $consultaMaestra
     * @return \App\Entity\Grupo
     */
    public function removeConsultaMaestra(ConsultaMaestra $consultaMaestra)
    {
        $this->consultaMaestras->removeElement($consultaMaestra);

        return $this;
    }

    /**
     * Get ConsultaMaestra entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsultaMaestras()
    {
        return $this->consultaMaestras;
    }


    /**
     * Add Usuario entity to collection.
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Grupo
     */
    public function addUsuario(Usuario $usuario)
    {
        $this->usuarios[] = $usuario;
        $usuario->addGrupo($this);
        return $this;
    }

    /**
     * Remove Usuario entity from collection.
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Grupo
     */
    public function removeUsuario(Usuario $usuario)
    {
        $grupo->removeUsuario($this);
        $this->usuarios->removeElement($usuario);
        
        return $this;
    }

    /**
     * Get Usuario entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'modo', 'estado_id');
    }
}
