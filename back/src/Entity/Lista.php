<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use App\Controller\Listas;
use App\Controller\ListaSave;

/**
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *    collectionOperations={
 *      "post"={"method"="POST"},
 *      "listas"={
 *          "method"="GET",
 *          "path"="/listas",
 *          "controller"=Listas::class,
 *          "defaults"={"_api_receive"=false}
*          }
 *    },
 *   itemOperations={
 *      "put"={
 *         "method"="PUT",
 *         "path"="/listas/{id}",
*          "controller"=ListaSave::class,
*          "defaults"={"_api_receive"=false}
 *      },
 *      "get"={
 *         "method"="GET",
 *         "path"="/listas/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *   }
 * )
 * 
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre"},
 *      arguments={"orderParameterName"="order"}
 * )
 * 
 * App\Entity\Lista
 *
 * @ORM\Entity(repositoryClass="App\Repository\ListaRepository")
 * @ORM\Table(name="lista")
 */
class Lista
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="CampoFormulario", mappedBy="lista")
     * @ORM\JoinColumn(name="id", referencedColumnName="lista_id", nullable=false)
     */
    protected $campoFormularios;

    /**
     * @ORM\OneToMany(targetEntity="CampoFormularioVersion", mappedBy="lista")
     * @ORM\JoinColumn(name="id", referencedColumnName="lista_id", nullable=false)
     */
    protected $campoFormulariosVersion;

    /**
     * @ORM\OneToMany(targetEntity="DetalleLista", mappedBy="lista")
     * @ORM\JoinColumn(name="id", referencedColumnName="lista_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $detalleListas;

    public function __construct()
    {
        $this->campoFormularios = new ArrayCollection();
        $this->campoFormulariosVersion = new ArrayCollection();
        $this->detalleListas = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Lista
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
     * @return \App\Entity\Lista
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
     * @return \App\Entity\Lista
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
     * Add CampoFormulario entity to collection (one to many).
     *
     * @param \App\Entity\CampoFormulario $campoFormulario
     * @return \App\Entity\Lista
     */
    public function addCampoFormulario(CampoFormulario $campoFormulario)
    {
        $this->campoFormularios[] = $campoFormulario;

        return $this;
    }

    /**
     * Remove CampoFormulario entity from collection (one to many).
     *
     * @param \App\Entity\CampoFormulario $campoFormulario
     * @return \App\Entity\Lista
     */
    public function removeCampoFormulario(CampoFormulario $campoFormulario)
    {
        $this->campoFormularios->removeElement($campoFormulario);

        return $this;
    }

    /**
     * Get CampoFormulario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCampoFormularios()
    {
        return $this->campoFormularios;
    }

    /**
     * Add CampoFormularioVersion entity to collection (one to many).
     *
     * @param \App\Entity\CampoFormularioVersion $campoFormularioVersion
     * @return \App\Entity\Lista
     */
    public function addCampoFormularioVersion(CampoFormularioVersion $campoFormularioVersion)
    {
        $this->campoFormulariosVersion[] = $campoFormularioVersion;

        return $this;
    }

    /**
     * Remove CampoFormularioVersion entity from collection (one to many).
     *
     * @param \App\Entity\CampoFormularioVersion $campoFormularioVersion
     * @return \App\Entity\Lista
     */
    public function removeCampoFormularioVersion(CampoFormularioVersion $campoFormularioVersion)
    {
        $this->campoFormulariosVersion->removeElement($campoFormularioVersion);

        return $this;
    }

    /**
     * Get CampoFormularioVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCampoFormulariosVersion()
    {
        return $this->campoFormulariosVersion;
    }

    /**
     * Add DetalleLista entity to collection (one to many).
     *
     * @param \App\Entity\DetalleLista $detalleLista
     * @return \App\Entity\Lista
     */
    public function addDetalleLista(DetalleLista $detalleLista)
    {
        $this->detalleListas[] = $detalleLista;

        return $this;
    }

    /**
     * Remove DetalleLista entity from collection (one to many).
     *
     * @param \App\Entity\DetalleLista $detalleLista
     * @return \App\Entity\Lista
     */
    public function removeDetalleLista(DetalleLista $detalleLista)
    {
        $this->detalleListas->removeElement($detalleLista);

        return $this;
    }

    /**
     * Get DetalleLista entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetalleListas()
    {
        return $this->detalleListas;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'estado_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'estado_id' => $this->getEstadoId()
        ];
    }
}
