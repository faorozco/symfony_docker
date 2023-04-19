<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\CargoExport;
use App\Controller\CargoImport;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
*     "import"={
 *         "method"="POST",
 *         "path"="/cargos/import",
 *         "controller"=CargoImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *     "export"={
 *          "method"="POST",
 *          "path"="/cargos/export",
 *          "controller"=CargoExport::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/cargos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
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
 * App\Entity\Cargo
 *
 * @ORM\Entity()
 * @ORM\Table(name="cargo")
 */
class Cargo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="cargo")
     * @ORM\JoinColumn(name="id", referencedColumnName="cargo_id", nullable=false)
     */
    protected $usuarios;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Cargo
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
     * @return \App\Entity\Cargo
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
     * Get the value of nombre.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->nombre;
    }

    public function __get($propertyName)
    {
        return $this->$propertyName;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Cargo
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
     * Add Usuario entity to collection (one to many).
     *
     * @param \App\Entity\Usuario $usuario
     * @return \App\Entity\Cargo
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
     * @return \App\Entity\Cargo
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
        return array('id', 'nombre', 'estado_id');
    }
}
