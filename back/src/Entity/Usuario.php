<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\Usuario\CargarGruposUsuario;
use App\Controller\Usuario\CargarUsuariosGrupo;
use App\Controller\Usuario\CargarUsuariosGruposFormularioVersion;
use App\Controller\Usuario\CargarUsuariosSistema;
use App\Controller\Usuario\ComponentesByUsuario;
use App\Controller\Usuario\UserByLogin;
use App\Controller\Usuario\UserImage;
use App\Controller\Usuario\UserImageViewer;
use App\Controller\Usuario\UserSpecial;
use App\Controller\Usuario\UserUpdateSpecial;
use App\Controller\Usuario\LogoutController;
use App\Controller\Usuario\UserGetAll;
use App\Controller\Usuario\UserGetAllPost;
use App\Controller\Usuario\UserGetEvents;
use App\Controller\Usuario\UserGetId;
use App\Controller\Usuario\UserGetOnlyList;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 *
 * @ApiResource(
 *   collectionOperations={
 *     "getAllw"={
 *         "method"="GET",
 *         "path"="/usuarios",
 *         "controller"=UserGetAll::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "new"={
 *         "method"="POST",
 *         "path"="/usuarios/special",
 *         "controller"=UserSpecial::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "getAllPost"={
 *         "method"="POST",
 *         "path"="/v2/users",
 *         "controller"=UserGetAllPost::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "getAllPostOnly"={
 *         "method"="POST",
 *         "path"="/search/only/users",
 *         "controller"=UserGetOnlyList::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "getAllEvents"={
 *         "method"="POST",
 *         "path"="/v2/users/event",
 *         "controller"=UserGetEvents::class,
 *         "defaults"={"_api_receive"=false}
 *     },
*       "get"={
*         "method"="GET",
*         "path"="/usuarios/{id}",
*         "controller"=UserGetId::class,
*          "requirements"={"id"="\d+"},
*          "defaults"={"_api_receive"=false}
*        }
 *   },
 *   itemOperations={
 *       "userimageget"={
 *         "method"="GET",
 *         "path"="/usuarios/{id}/imagen",
 *         "controller"=UserImageViewer::class,
 *         "defaults"={"_api_receive"=false}
 *       },
 *       "userimageupload"={
 *         "method"="POST",
 *         "path"="/usuarios/{id}/imagen",
 *         "controller"=UserImage::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "componentesbyusuario"={
 *         "method"="GET",
 *         "path"="/usuarios/{id}/componentesbyusuario",
 *         "controller"=ComponentesByUsuario::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "getbylogin"={
 *         "method"="GET",
 *         "path"="/usuarios/getbylogin",
 *         "controller"=UserByLogin::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "update"={
 *         "method"="PUT",
 *         "path"="/usuarios/{id}/special",
 *         "controller"=UserUpdateSpecial::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *      "logout"={
 *         "method"="GET",
 *         "path"="/usuarios/logout",
 *         "controller"=LogoutController::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "cargarUsuariosGrupo"={
 *         "method"="GET",
 *         "path"="/usuarios/{id}/usuarios_grupo",
 *         "controller"=CargarUsuariosGrupo::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "cargarUsuariosGruposFormularioVersion"={
 *         "method"="GET",
 *         "path"="/usuarios/{id}/usuarios_grupo_formulario",
 *         "controller"=CargarUsuariosGruposFormularioVersion::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *      "cargarUsuariosSystema"={
 *         "method"="GET",
 *         "path"="/usuarios/usuarios_sistema",
 *         "controller"=CargarUsuariosSistema::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *     "cargarGruposUsuario"={
 *         "method"="GET",
 *         "path"="/usuarios/{id}/grupos",
 *         "controller"=CargarGruposUsuario::class,
 *         "defaults"={"_api_receive"=false}
 *     }
 *   }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"login", "nombre1","nombre2", "apellido1", "apellido2"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"login", "nombre1","nombre2", "apellido1", "apellido2"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\Usuario
 *
 * @ORM\Entity()
 * @UniqueEntity(
 *     fields={"login"},
 *     message="Este login ya esta siendo usado."
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 * 
 * @ORM\Table(name="usuario", 
 * indexes={
 *  @ORM\Index(name="fk_usuario_cargo1_idx", columns={"cargo_id"}), 
 *  @ORM\Index(name="fk_usuario_proceso1_idx", columns={"proceso_id"}), 
 *  @ORM\Index(name="fk_usuario_sede1_idx", columns={"sede_id"})
 * }
 * )
 */
class Usuario implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`login`", type="string", length=45, nullable=true, unique=true)
     */
    protected $login;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $numero_documento;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $apellido1;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $apellido2;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $nombre1;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $nombre2;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $celular;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $telefono_fijo_residencia;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $direccion_residencia;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    protected $genero;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha_nacimiento;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $clave;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $imagen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $cargo_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $proceso_id;

    /**
     * @ORM\OneToMany(targetEntity="Auditoria", mappedBy="usuario")
     * @ORM\JoinColumn(name="id", referencedColumnName="usuario_id", nullable=false)
     */
    protected $auditorias;

    /**
     * @ORM\OneToOne(targetEntity="EstadoFlujo", mappedBy="creador")
     */
    protected $creador;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sede_id;

    /**
     * @ORM\ManyToOne(targetEntity="Sede", inversedBy="usuarios", cascade={"persist"})
     * @ORM\JoinColumn(name="sede_id", referencedColumnName="id", nullable=true)
     */
    protected $sede;

    /**
     * @ORM\OneToOne(targetEntity="EstadoFlujo", mappedBy="ultimoResponsable")
     */
    protected $ultimoResponsable;

    /**
     * @ORM\OneToOne(targetEntity="EstadoFlujo", mappedBy="responsablePaso")
     */
    protected $responsablePaso;

    /**
     * @ORM\OneToMany(targetEntity="Notificado", mappedBy="usuario")
     * @ORM\JoinColumn(name="id", referencedColumnName="usuario_id", nullable=false)
     */
    protected $notificados;

    /**
     * @ORM\OneToMany(targetEntity="Registro", mappedBy="usuario")
     * @ORM\JoinColumn(name="id", referencedColumnName="usuario_id", nullable=false)
     */
    protected $registros;

    /**
     * Muchos Usuarios tienen muchos Rols
     * @ORM\ManyToMany(targetEntity="Rol", inversedBy="usuarios")
     * @ORM\JoinTable(
     *  name="usuario_rol",
     *  joinColumns={
     *      @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="rol_id", referencedColumnName="id")
     *  }
     * )
     * @ApiSubresource(maxDepth=1)
     */
    protected $rols;

    /**
     * @ORM\ManyToOne(targetEntity="Cargo", inversedBy="usuarios", cascade={"persist"})
     * @ORM\JoinColumn(name="cargo_id", referencedColumnName="id", nullable=false)
     */
    protected $cargo;

    /**
     * @ORM\ManyToOne(targetEntity="Proceso", inversedBy="usuarios")
     * @ORM\JoinColumn(name="proceso_id", referencedColumnName="id", nullable=false)
     */
    protected $proceso;

    /**
     * @ORM\ManyToMany(targetEntity="Grupo", mappedBy="usuarios")
     */
    protected $grupos;

    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="usuario")
     * @ORM\JoinColumn(name="id", referencedColumnName="usuario_id", nullable=false)
     */
    protected $comentarios;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenValidAfter;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activeSesion;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $bloqueo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $try;

    public function __construct()
    {
        $this->auditorias = new ArrayCollection();
        $this->notificados = new ArrayCollection();
        $this->registros = new ArrayCollection();
        $this->rols = new ArrayCollection();
        $this->grupos = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Usuario
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
     * Set the value of login.
     *
     * @param string $login
     * @return \App\Entity\Usuario
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get the value of login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function getPassword()
    {
        return $this->clave;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Set the value of numero_documento.
     *
     * @param string $numero_documento
     * @return \App\Entity\Usuario
     */
    public function setNumeroDocumento($numero_documento)
    {
        $this->numero_documento = $numero_documento;

        return $this;
    }

    /**
     * Get the value of numero_documento.
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numero_documento;
    }

    /**
     * Set the value of apellido1.
     *
     * @param string $apellido1
     * @return \App\Entity\Usuario
     */
    public function setApellido1($apellido1)
    {
        $this->apellido1 = $apellido1;

        return $this;
    }

    /**
     * Get the value of apellido1.
     *
     * @return string
     */
    public function getApellido1()
    {
        return $this->apellido1;
    }

    /**
     * Set the value of apellido2.
     *
     * @param string $apellido2
     * @return \App\Entity\Usuario
     */
    public function setApellido2($apellido2)
    {
        $this->apellido2 = $apellido2;

        return $this;
    }

    /**
     * Get the value of apellido2.
     *
     * @return string
     */
    public function getApellido2()
    {
        return $this->apellido2;
    }

    /**
     * Set the value of nombre1.
     *
     * @param string $nombre1
     * @return \App\Entity\Usuario
     */
    public function setNombre1($nombre1)
    {
        $this->nombre1 = $nombre1;

        return $this;
    }

    /**
     * Get the value of nombre1.
     *
     * @return string
     */
    public function getNombre1()
    {
        return $this->nombre1;
    }

    /**
     * Set the value of nombre2.
     *
     * @param string $nombre2
     * @return \App\Entity\Usuario
     */
    public function setNombre2($nombre2)
    {
        $this->nombre2 = $nombre2;

        return $this;
    }

    /**
     * Get the value of nombre2.
     *
     * @return string
     */
    public function getNombre2()
    {
        return $this->nombre2;
    }

    /**
     * Set the value of celular.
     *
     * @param integer $celular
     * @return \App\Entity\Usuario
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
     * @return \App\Entity\Usuario
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
     * Set the value of telefono_fijo_residencia.
     *
     * @param integer $telefono_fijo_residencia
     * @return \App\Entity\Usuario
     */
    public function setTelefonoFijoResidencia($telefono_fijo_residencia)
    {
        $this->telefono_fijo_residencia = $telefono_fijo_residencia;

        return $this;
    }

    /**
     * Get the value of telefono_fijo_residencia.
     *
     * @return integer
     */
    public function getTelefonoFijoResidencia()
    {
        return $this->telefono_fijo_residencia;
    }

    /**
     * Set the value of direccion_residencia.
     *
     * @param string $direccion_residencia
     * @return \App\Entity\Usuario
     */
    public function setDireccionResidencia($direccion_residencia)
    {
        $this->direccion_residencia = $direccion_residencia;

        return $this;
    }

    /**
     * Get the value of direccion_residencia.
     *
     * @return string
     */
    public function getDireccionResidencia()
    {
        return $this->direccion_residencia;
    }

    /**
     * Set the value of genero.
     *
     * @param string $genero
     * @return \App\Entity\Usuario
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get the value of genero.
     *
     * @return string
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set the value of fecha_nacimiento.
     *
     * @param \DateTime $fecha_nacimiento
     * @return \App\Entity\Usuario
     */
    public function setFechaNacimiento($fecha_nacimiento)
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    /**
     * Get the value of fecha_nacimiento.
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
    }

    /**
     * Set the value of clave.
     *
     * @param string $clave
     * @return \App\Entity\Usuario
     */
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get the value of clave.
     *
     * @return string
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set the value of imagen.
     *
     * @param string $imagen
     * @return \App\Entity\Usuario
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get the value of imagen.
     *
     * @return string
     */
    public function getImagen()
    {
        if (null !== $this->imagen) {
            return $this->imagen;
        } else {
            return $_ENV["DEFAULT_AVATAR"];
        }
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Usuario
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
     * Set the value of cargo_id.
     *
     * @param integer $cargo_id
     * @return \App\Entity\Usuario
     */
    public function setCargoId($cargo_id)
    {
        $this->cargo_id = $cargo_id;

        return $this;
    }

    /**
     * Get the value of cargo_id.
     *
     * @return integer
     */
    public function getCargoId()
    {
        return $this->cargo_id;
    }

    /**
     * Set the value of proceso_id.
     *
     * @param integer $proceso_id
     * @return \App\Entity\Usuario
     */
    public function setProcesoId($proceso_id)
    {
        $this->proceso_id = $proceso_id;

        return $this;
    }

    /**
     * Get the value of proceso_id.
     *
     * @return integer
     */
    public function getProcesoId()
    {
        return $this->proceso_id;
    }

    /**
     * Add Auditoria entity to collection (one to many).
     *
     * @param \App\Entity\Auditoria $auditoria
     * @return \App\Entity\Usuario
     */
    public function addAuditoria(Auditoria $auditoria)
    {
        $this->auditorias[] = $auditoria;

        return $this;
    }

    /**
     * Remove Auditoria entity from collection (one to many).
     *
     * @param \App\Entity\Auditoria $auditoria
     * @return \App\Entity\Usuario
     */
    public function removeAuditoria(Auditoria $auditoria)
    {
        $this->auditorias->removeElement($auditoria);

        return $this;
    }

    /**
     * Get Auditoria entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuditorias()
    {
        return $this->auditorias;
    }

    /**
     * Add Notificado entity to collection (one to many).
     *
     * @param \App\Entity\Notificado $notificado
     * @return \App\Entity\Usuario
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
     * @return \App\Entity\Usuario
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
     * Add Registro entity to collection (one to many).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Usuario
     */
    public function addRegistro(Registro $registro)
    {
        $this->registros[] = $registro;

        return $this;
    }

    /**
     * Remove Registro entity from collection (one to many).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Usuario
     */
    public function removeRegistro(Registro $registro)
    {
        $this->registros->removeElement($registro);

        return $this;
    }

    /**
     * Get Registro entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistros()
    {
        return $this->registros;
    }

    /**
     * Add Rol entity to collection (one to many).
     *
     * @param \App\Entity\Rol $rol
     * @return \App\Entity\Usuario
     */
    public function addRol(Rol $rol)
    {
        $this->rols[] = $rol;
        return $this;
    }

    /**
     * Remove Rol entity from collection (one to many).
     *
     * @param \App\Entity\Rol $rol
     * @return \App\Entity\Usuario
     */
    public function removeRol(Rol $rol)
    {
        $this->rols->removeElement($rol);
        return $this;
    }

    /**
     * Get UsuarioRol entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRols()
    {
        return $this->rols;
    }

    /**
     * Set Cargo entity (many to one).
     *
     * @param \App\Entity\Cargo $cargo
     * @return \App\Entity\Usuario
     */
    public function setCargo(Cargo $cargo = null)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get Cargo entity (many to one).
     *
     * @return \App\Entity\Cargo
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set Proceso entity (many to one).
     *
     * @param \App\Entity\Proceso $proceso
     * @return \App\Entity\Usuario
     */
    public function setProceso(Proceso $proceso = null)
    {
        $this->proceso = $proceso;

        return $this;
    }

    /**
     * Get Proceso entity (many to one).
     *
     * @return \App\Entity\Proceso
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     * Add Grupo entity to collection.
     *
     * @param \App\Entity\Grupo $grupo
     * @return \App\Entity\Usuario
     */
    public function addGrupo(Grupo $grupo)
    {
        $this->grupos[] = $grupo;
        return $this;
    }

    /**
     * Remove Grupo entity from collection.
     *
     * @param \App\Entity\Grupo $grupo
     * @return \App\Entity\Usuario
     */
    public function removeGrupo(Grupo $grupo)
    {
        $this->grupos->removeElement($grupo);

        return $this;
    }

    /**
     * Get Grupo entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * Add Comentario entity to collection.
     *
     * @param \App\Entity\Comentario $comentario
     * @return \App\Entity\Usuario
     */
    public function addComentario(Comentario $comentario)
    {
        $this->grupos[] = $grupo;
        return $this;
    }

    /**
     * Remove Comentario entity from collection.
     *
     * @param \App\Entity\Comentario $comentario
     * @return \App\Entity\Usuario
     */
    public function removeComentario(Comentario $comentario)
    {
        $comentario->removeUsuario($this);
        $this->comentarios->removeElement($comentario);

        return $this;
    }

    /**
     * Get Comentario entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->login,
            $this->clave,
            $this->estadoId,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->login,
            $this->clave,
            $this->estadoId
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * Set the value of sede_id.
     *
     * @param integer $sede_id
     * @return \App\Entity\Usuario
     */
    public function setSedeId($sede_id)
    {
        $this->sede_id = $sede_id;

        return $this;
    }

    /**
     * Get the value of sede_id.
     *
     * @return integer
     */
    public function getSedeId()
    {
        return $this->sede_id;
    }

    /**
     * Set Sede entity (one to one).
     *
     * @param \App\Entity\Sede $sede
     * @return \App\Entity\Usuario
     */
    public function setSede(Sede $sede = null)
    {
        $this->sede = $sede;

        return $this;
    }

    /**
     * Get Sede entity (many to one).
     *
     * @return \App\Entity\Sede
     */
    public function getSede()
    {
        return $this->sede;
    }

    public function __sleep()
    {
        return array('id', 'login', 'numero_documento', 'apellido1', 'apellido2', 'nombre1', 'nombre2', 'celular', 'email', 'telefono_fijo_residencia', 'direccion_residencia', 'genero', 'fecha_nacimiento', 'clave', 'imagen', 'estado_id', 'cargo_id', 'proceso_id', 'sede_id');
    }


    public function getTokenValidAfter(): ?\DateTimeInterface
    {
        return $this->tokenValidAfter;
    }

    public function setTokenValidAfter(?\DateTimeInterface $tokenValidAfter): self
    {
        $this->tokenValidAfter = $tokenValidAfter;

        return $this;
    }

    public function getActiveSesion(): ?bool
    {
        return $this->activeSesion;
    }

    public function setActiveSesion(?bool $activeSesion): self
    {
        $this->activeSesion = $activeSesion;

        return $this;
    }

    public function getBloqueo(): ?bool
    {
        return $this->bloqueo;
    }

    public function setBloqueo(?bool $bloqueo): self
    {
        $this->bloqueo = $bloqueo;

        return $this;
    }

    public function getTry(): ?int
    {
        return $this->try;
    }

    public function setTry(?int $try): self
    {
        $this->try = $try;

        return $this;
    }

}
