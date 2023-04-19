<?php



namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put",
 *      "delete",
 *      "get"={
 *         "method"="GET",
 *         "path"="/tipo_correspondencias/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\TipoCorrespondencia
 *
 * @ORM\Entity()
 * @ORM\Table(name="tipo_correspondencia")
 */
class TipoCorrespondencia
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $consecutivo;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\TipoCorrespondencia
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
     * @return \App\Entity\TipoCorrespondencia
     *  @ORM\PostPersist
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
     * @return \App\Entity\TipoCorrespondencia
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
     * Get the value of consecutivo
     */
    public function getConsecutivo()
    {
        return $this->consecutivo;
    }

    /**
     * Set the value of consecutivo
     *
     * @return  self
     */
    public function setConsecutivo($consecutivo)
    {
        $this->consecutivo = $consecutivo;

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    public function newYear()
    {
        $date = new \DateTime();
        $currentYear = $date->format("Y");
        if ($this->getYear() < $currentYear) {
            return true;
        } else {
            return false;
        }
    }

    public function changeYear(){
        $date = new \DateTime();
        $currentYear = $date->format("Y");
        $this->setYear($currentYear);
    }

    public function incrementarConsecutivo(){
        $this->setConsecutivo($this->getConsecutivo()+1);
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'consecutivo', 'year', 'estado_id');
    }
}
