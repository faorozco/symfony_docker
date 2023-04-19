<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
 *         "path"="/carpetas/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *  }
 * )
 * App\Entity\Carpeta
 *
 * @ORM\Entity()
 * @ORM\Table(name="carpeta")
 */
class Carpeta
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $identificador;

    /**
     * @ORM\OneToMany(targetEntity="Archivo", mappedBy="carpeta")
     * @ORM\JoinColumn(name="id", referencedColumnName="carpeta_id", nullable=false)
     */
    protected $archivos;

    public function __construct()
    {
        $this->archivos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Carpeta
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
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\Carpeta
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of identificador.
     *
     * @param string $identificador
     * @return \App\Entity\Carpeta
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;

        return $this;
    }

    /**
     * Get the value of identificador.
     *
     * @return string
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Add Archivo entity to collection (one to many).
     *
     * @param \App\Entity\Archivo $archivo
     * @return \App\Entity\Carpeta
     */
    public function addArchivo(Archivo $archivo)
    {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove Archivo entity from collection (one to many).
     *
     * @param \App\Entity\Archivo $archivo
     * @return \App\Entity\Carpeta
     */
    public function removeArchivo(Archivo $archivo)
    {
        $this->archivos->removeElement($archivo);

        return $this;
    }

    /**
     * Get Archivo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArchivos()
    {
        return $this->archivos;
    }

    public function __sleep()
    {
        return array('id', 'descripcion', 'identificador', 'carpetacol');
    }
}
