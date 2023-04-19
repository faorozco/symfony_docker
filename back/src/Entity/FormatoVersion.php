<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\ORSearchFilter;
use App\Controller\FormatoGeneratePdf;
use App\Controller\StoreFormat;
use App\Controller\FormatoSave;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={ 
 *          "method"="POST",
 *          "path"="/formatosVersion",
 *          "controller"=FormatoSave::class
 *       },
 *      "storeformats"={
 *          "method"="POST",
 *          "path"="/formatosVersion/{id}/store",
 *          "controller"=StoreFormat::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "generarpdfformatos"={
 *          "method"="POST",
 *          "path"="/formatosVersion/{id}/generatepdf",
 *          "controller"=FormatoGeneratePdf::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/formatosVersion/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"cuando","titulo","contenido"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"cuando","titulo","contenido"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Entidad
 * App\Entity\FormatoVersion
 *
 * @ORM\Entity(repositoryClass="App\Repository\FormatoVersionRepository")
 * @ORM\Table(name="formato_version", indexes={@ORM\Index(name="fk_Registro_numerico_Registro1_idx", columns={"registro_id"}), @ORM\Index(name="fk_formato_version_plantilla_version1_idx", columns={"plantilla_version_id"}), @ORM\Index(name="fk_formato_version_archivo1_idx", columns={"archivo_id"})})
 */
class FormatoVersion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $cuando;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $titulo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $contenido;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $fecha_hora_impresion;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registro_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $plantilla_version_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $archivo_id;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="formatosVersion")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    /**
     * @ORM\ManyToOne(targetEntity="PlantillaVersion", inversedBy="formatosVersion")
     * @ORM\JoinColumn(name="plantilla_id", referencedColumnName="id", nullable=false)
     */
    protected $plantillaVersion;

    /**
     * @ORM\OneToOne(targetEntity="Archivo", inversedBy="formatoVersion")
     * @ORM\JoinColumn(name="archivo_id", referencedColumnName="id")
     */
    protected $archivo;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Formato
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
     * Set the value of cuando.
     *
     * @param \DateTime $cuando
     * @return \App\Entity\Formato
     */
    public function setCuando($cuando)
    {
        $this->cuando = $cuando;

        return $this;
    }

    /**
     * Get the value of cuando.
     *
     * @return \DateTime
     */
    public function getCuando()
    {
        return $this->cuando;
    }

    /**
     * Set the value of titulo.
     *
     * @param string $titulo
     * @return \App\Entity\Formato
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get the value of titulo.
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set the value of contenido.
     *
     * @param string $contenido
     * @return \App\Entity\Formato
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get the value of contenido.
     *
     * @return string
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * Get the value of fecha_hora_impresion
     */
    public function getFechaHoraImpresion()
    {
        return $this->fecha_hora_impresion;
    }

    /**
     * Set the value of fecha_hora_impresion
     *
     * @return  self
     */
    public function setFechaHoraImpresion($fecha_hora_impresion)
    {
        $this->fecha_hora_impresion = $fecha_hora_impresion;

        return $this;
    }

    /**
     * Set the value of registro_id.
     *
     * @param integer $registro_id
     * @return \App\Entity\Formato
     */
    public function setRegistroId($registro_id)
    {
        $this->registro_id = $registro_id;

        return $this;
    }

    /**
     * Get the value of registro_id.
     *
     * @return integer
     */
    public function getRegistroId()
    {
        return $this->registro_id;
    }

    /**
     * Set the value of plantilla_version_id.
     *
     * @param integer $plantilla_version_id
     * @return \App\Entity\Formato
     */
    public function setPlantillaVersionId($plantilla_version_id)
    {
        $this->plantilla_version_id = $plantilla_version_id;

        return $this;
    }

    /**
     * Get the value of plantilla_version_id.
     *
     * @return integer
     */
    public function getPlantillaVersionId()
    {
        return $this->plantilla_version_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Formato
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
     * Set the value of archivo_id.
     *
     * @param integer $archivo_id
     * @return \App\Entity\Formato
     */
    public function setArchivoId($archivo_id)
    {
        $this->archivo_id = $archivo_id;

        return $this;
    }

    /**
     * Get the value of archivo_id.
     *
     * @return integer
     */
    public function getArchivoId()
    {
        return $this->archivo_id;
    }

    /**
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Formato
     */
    public function setRegistro(Registro $registro = null)
    {
        $this->registro = $registro;

        return $this;
    }

    /**
     * Get Registro entity (many to one).
     *
     * @return \App\Entity\Registro
     */
    public function getRegistro()
    {
        return $this->registro;
    }

    /**
     * Set PlantillaVersion entity (many to one).
     *
     * @param \App\Entity\PlantillaVersion $plantillaVersion
     * @return \App\Entity\Formato
     */
    public function setPlantillaVersion(PlantillaVersion $plantillaVersion = null)
    {
        $this->plantillaVersion = $plantillaVersion;

        return $this;
    }

    /**
     * Get PlantillaVersion entity (many to one).
     *
     * @return \App\Entity\PlantillaVersion
     */
    public function getPlantillaVersion()
    {
        return $this->plantillaVersion;
    }

    /**
     * Set Archivo entity (one to one).
     *
     * @param \App\Entity\Archivo $archivo
     * @return \App\Entity\Formato
     */
    public function setArchivo(Archivo $archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * Get Archivo entity (one to one).
     *
     * @return \App\Entity\Archivo
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    public function __sleep()
    {
        return array('id', 'cuando', 'titulo', 'contenido', 'registro_id', 'plantilla_version_id', 'estado_id', 'archivo_id');
    }

}
