<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\DownloadFile;
use App\Controller\RemoteFile;
use App\Controller\VersionarArchivo;
use App\Controller\HistoricoArchivo;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\EjecucionPaso\GetFiles;
use App\Controller\EjecucionPaso\DeleteFiles;
use App\Controller\EjecucionPaso\GetUpForm;
use App\Controller\EjecucionPaso\CambiarArchivo;



/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *        "getremotefiles"={
 *         "method"="GET",
 *         "path"="/archivos/getremotefiles",
 *         "controller"=RemoteFile::class
 *        }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/archivos/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *        "downloadfile"={
 *         "method"="GET",
 *         "path"="/archivos/{id}/download",
 *         "controller"=DownloadFile::class
 *        },
 *        "versionarArchivo"={
 *         "method"="POST",
 *         "path"="/archivos/versionar",
 *         "controller"=VersionarArchivo::class,
 *          "defaults"={"_api_receive"=false}
 *        },
 *        "cambiarArchivo"={
 *         "method"="POST",
 *         "path"="/archivos/cambiar",
 *         "controller"=CambiarArchivo::class,
 *          "defaults"={"_api_receive"=false}
 *        },
*         "verHistorico"={
 *         "method"="POST",
 *         "path"="/archivos/historico",
 *         "controller"=HistoricoArchivo::class,
 *          "defaults"={"_api_receive"=false}
 *        },
 *        "ObtenerArchivosEjecucion"={ 
 *          "method"="POST",
 *          "path"="/ejecucion_pasos/get_files",
 *          "controller"=GetFiles::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *        "DeleteArchivos"={ 
 *          "method"="POST",
 *          "path"="/ejecucion_pasos/delete_files",
 *          "controller"=DeleteFiles::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *        "GetUpFileFlujo"={ 
 *          "method"="POST",
 *          "path"="/ejecucion_pasos/get_relationship",
 *          "controller"=GetUpForm::class,
 *          "defaults"={"_api_receive"=false}
 *       }
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre","comentario","fecha_version"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre","comentario","fecha_version"},
 *      arguments={"orderParameterName"="order"})
 * App\Entity\Archivo
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArchivoRepository")
 * @ORM\Table(name="archivo", indexes={@ORM\Index(name="fk_Registro_numerico_Registro1_idx", columns={"registro_id"}), @ORM\Index(name="fk_archivo_carpeta1_idx", columns={"carpeta_id"})})
 */
class Archivo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $identificador;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $version;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha_version;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $comentario;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registro_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $carpeta_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $tipo_documental;

    /**
     * @ORM\OneToOne(targetEntity="Formato", mappedBy="archivo")
     */
    protected $formato;

    /**
     * @ORM\ManyToOne(targetEntity="Registro", inversedBy="archivos")
     * @ORM\JoinColumn(name="registro_id", referencedColumnName="id", nullable=false)
     */
    protected $registro;

    /**
     * @ORM\ManyToOne(targetEntity="Carpeta", inversedBy="archivos")
     * @ORM\JoinColumn(name="carpeta_id", referencedColumnName="id", nullable=false)
     */
    protected $carpeta;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $tipo_archivo;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha_vigencia;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $vigente;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $archivo_origen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ejecucion_paso_id;


    public function __construct()
    {
        $this->formatos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Archivo
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
     * @return \App\Entity\Archivo
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
     * Set the value of identificador.
     *
     * @param string $identificador
     * @return \App\Entity\Archivo
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
     * Set the value of version.
     *
     * @param integer $version
     * @return \App\Entity\Archivo
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the value of version.
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of fecha_version.
     *
     * @param \DateTime $fecha_version
     * @return \App\Entity\Archivo
     */
    public function setFechaVersion($fecha_version)
    {
        $this->fecha_version = $fecha_version;

        return $this;
    }

    /**
     * Get the value of fecha_version.
     *
     * @return \DateTime
     */
    public function getFechaVersion()
    {
        return $this->fecha_version;
    }

    /**
     * Set the value of comentario.
     *
     * @param string $comentario
     * @return \App\Entity\Archivo
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get the value of comentario.
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set the value of registro_id.
     *
     * @param integer $registro_id
     * @return \App\Entity\Archivo
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
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Archivo
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
     * Set the value of carpeta_id.
     *
     * @param integer $carpeta_id
     * @return \App\Entity\Archivo
     */
    public function setCarpetaId($carpeta_id)
    {
        $this->carpeta_id = $carpeta_id;

        return $this;
    }

    /**
     * Get the value of carpeta_id.
     *
     * @return integer
     */
    public function getCarpetaId()
    {
        return $this->carpeta_id;
    }

    /**
     * Set Formato entity (one to one).
     *
     * @param \App\Entity\Formato $formato
     * @return \App\Entity\Archivo
     */
    public function setFormato(Formato $formato = null)
    {
        $formato->setArchivo($this);
        $this->formato = $formato;

        return $this;
    }

    /**
     * Get Formato entity (one to one).
     *
     * @return \App\Entity\Formato
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * Set Registro entity (many to one).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\Archivo
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
     * Set Carpeta entity (many to one).
     *
     * @param \App\Entity\Carpeta $carpeta
     * @return \App\Entity\Archivo
     */
    public function setCarpeta(Carpeta $carpeta = null)
    {
        $this->carpeta = $carpeta;

        return $this;
    }

    /**
     * Get Carpeta entity (many to one).
     *
     * @return \App\Entity\Carpeta
     */
    public function getCarpeta()
    {
        return $this->carpeta;
    }

    /**
     * Get the value of tipo_documental
     */
    public function getTipoDocumental()
    {
        return $this->tipo_documental;
    }

    /**
     * Set the value of tipo_documental
     *
     * @return  self
     */
    public function setTipoDocumental($tipo_documental)
    {
        $this->tipo_documental = $tipo_documental;

        return $this;
    }

    /**
     * Set the value of tipo_archivo.
     *
     * @param string $tipo_archivo
     * @return \App\Entity\Archivo
     */
    public function setTipoArchivo($tipo_archivo)
    {
        $this->tipo_archivo = $tipo_archivo;

        return $this;
    }

    /**
     * Get the value of tipo_archivo.
     *
     * @return string
     */
    public function getTipoArchivo()
    {
        return $this->tipo_archivo;
    }

    /**
     * Set the value of fecha_vigencia.
     *
     * @param \DateTime $fecha_vigencia
     * @return \App\Entity\Archivo
     */
    public function setFechaVigencia($fecha_vigencia)
    {
        $this->fecha_vigencia = $fecha_vigencia;

        return $this;
    }

    /**
     * Get the value of fecha_vigencia.
     *
     * @return \DateTime
     */
    public function getFechaVigencia()
    {
        return $this->fecha_vigencia;
    }

    /**
     * Set the value of vigente.
     *
     * @param integer $vigente
     * @return \App\Entity\Archivo
     */
    public function setVigente($vigente)
    {
        $this->vigente = $vigente;

        return $this;
    }

    /**
     * Get the value of vigente.
     *
     * @return integer
     */
    public function getVigente()
    {
        return $this->vigente;
    }

    /**
     * Set the value of archivo_origen.
     *
     * @param integer $archivo_origen
     * @return \App\Entity\Archivo
     */
    public function setArchivoOrigen($archivo_origen)
    {
        $this->archivo_origen = $archivo_origen;

        return $this;
    }

    /**
     * Get the value of vigente.
     *
     * @return integer
     */
    public function getArchivoOrigen()
    {
        return $this->archivo_origen;
    }

    public function __sleep()
    {
        return array('id', 'version', 'fecha_version', 'comentario', 'registro_id', 'estado_id', 'carpeta_id', 'tipo_documental', 'tipo_archivo', 'fecha_vigencia', 'vigente', 'archivo_origen');
    }

    public function getEjecucionPasoId(): ?int
    {
        return $this->ejecucion_paso_id;
    }

    public function setEjecucionPasoId(?int $ejecucion_paso_id): self
    {
        $this->ejecucion_paso_id = $ejecucion_paso_id;

        return $this;
    }
}
