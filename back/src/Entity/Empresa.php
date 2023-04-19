<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\EnterpriseImage;
use App\Controller\EnterpriseImageViewer;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *     "get"={"method"="GET"},
 *     "enterpriseimageupload"={
 *         "method"="POST",
 *         "path"="/empresas/{id}/imagen",
 *         "controller"=EnterpriseImage::class,
 *         "defaults"={"_api_receive"=false}
 *     },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/empresas/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *     "enterpriseimageget"={
 *         "method"="GET",
 *         "path"="/empresas/{id}/imagen",
 *         "controller"=EnterpriseImageViewer::class,
 *         "defaults"={"_api_receive"=false}
 *     }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={
 *          "nit","nombre","direccion","pbx","celular","email","url","imagen"
 *      }
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={
 *          "nit","nombre","direccion","pbx","celular","email","url","imagen"
 *      },
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\Empresa
 *
 * @ORM\Entity()
 * @ORM\Table(name="empresa")
 */
class Empresa
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $nit;

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
     * @ORM\Column(type="bigint", nullable=true)
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $imagen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\OneToMany(targetEntity="Sede", mappedBy="empresa")
     * @ORM\JoinColumn(name="id", referencedColumnName="empresa_id", nullable=false)
     * @ApiSubresource
     */
    protected $sedes;

    public function __construct()
    {
        $this->sedes = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Empresa
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
     * Set the value of nit.
     *
     * @param integer $nit
     * @return \App\Entity\Empresa
     */
    public function setNit($nit)
    {
        $this->nit = $nit;

        return $this;
    }

    /**
     * Get the value of nit.
     *
     * @return integer
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\Empresa
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
     * @return \App\Entity\Empresa
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
     * @return \App\Entity\Empresa
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
     * @return \App\Entity\Empresa
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
     * @return \App\Entity\Empresa
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
     * @return \App\Entity\Empresa
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
     * Set the value of imagen.
     *
     * @param string $imagen
     * @return \App\Entity\Empresa
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
        return $this->imagen;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Empresa
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
     * Add Sede entity to collection (one to many).
     *
     * @param \App\Entity\Sede $sede
     * @return \App\Entity\Empresa
     */
    public function addSede(Sede $sede)
    {
        $this->sedes[] = $sede;

        return $this;
    }

    /**
     * Remove Sede entity from collection (one to many).
     *
     * @param \App\Entity\Sede $sede
     * @return \App\Entity\Empresa
     */
    public function removeSede(Sede $sede)
    {
        $this->sedes->removeElement($sede);

        return $this;
    }

    /**
     * Get Sede entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSedes()
    {
        return $this->sedes;
    }


    public function __sleep()
    {
        return array('id', 'nit', 'nombre', 'direccion', 'pbx', 'celular', 'email', 'url', 'imagen', 'estado_id');
    }
}
