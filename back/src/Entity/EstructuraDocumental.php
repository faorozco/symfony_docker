<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\DocumentosEstructuraDocumental;
use App\Controller\DuplicateCodigoDirectorio;
use App\Controller\EstructuraDocumentalByNode;
use App\Controller\EstructuraDocumentalExport;
use App\Controller\EstructuraDocumentalImport;
use App\Controller\EstructuraDocumentalNonRelated;
use App\Controller\EstructuraDocumentalSave;
use App\Controller\EstructuraDocumentalXls;
use App\Controller\InactivateNode;
use App\Controller\TipoDocumental;
use App\Controller\TiposDocumentales;
use App\Controller\EstructuraDocumentalRuta;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={
 *          "method"="GET",
 *          "maximum_items_per_page"=100
 *      },
 *      "post"={"method"="POST"},
 *      "getestructurabynode"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/getestructurabynode",
 *         "controller"=EstructuraDocumentalByNode::class
 *      },
 *      "getxlstrd"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/getxlstrd/{nodo}/{version}",
 *         "controller"=EstructuraDocumentalXls::class
 *      },
 *      "getnonrelated"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/nonrelated",
 *         "controller"=EstructuraDocumentalNonRelated::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "gettiposdocumentales"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/tiposdocumentals/{id}",
 *         "controller"=TiposDocumentales::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *     "checkduplicatecodigodirectorio"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/codigodirectorio/{codigo_directorio}/checkduplicate/{esActualizacion}",
 *         "controller"=DuplicateCodigoDirectorio::class,
 *      },
 *     "export"={
 *          "method"="POST",
 *          "path"="/estructura_documentals/export",
 *          "controller"=EstructuraDocumentalExport::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *     "save"={
 *          "method"="POST",
 *          "path"="/estructura_documentals/save",
 *          "controller"=EstructuraDocumentalSave::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "import"={
 *         "method"="POST",
 *         "path"="/estructura_documentals/import",
 *         "controller"=EstructuraDocumentalImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "getregistrosbyestructura"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/{id}/getdocuments",
 *         "controller"=DocumentosEstructuraDocumental::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          } 
 *      },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *     "gettipodocumentalbyid"={
 *         "method"="GET",
 *         "path"="/estructura_documentals/tipodocumental/{id}",
 *         "controller"=TipoDocumental::class,
 *      },
 *      "inactivate"={
 *          "method"="POST",
 *          "path"="/estructura_documentals/{id}/inactivate",
 *          "controller"=InactivateNode::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "getWithRoute"={
 *          "method"="GET",
 *          "path"="/estructura_documentals/{id}/route",
 *          "controller"=EstructuraDocumentalRuta::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"descripcion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"descripcion"},
 *      arguments={"orderParameterName"="order"}
 * )
 * App\Entity\EstructuraDocumental
 *
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="App\Repository\EstructuraDocumentalRepository")
 * @ORM\Table(name="estructura_documental")
 * @ORM\HasLifecycleCallbacks()
 */
class EstructuraDocumental
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
    protected $codigo_directorio_padre;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $codigo_directorio;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $idestructura;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $peso;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $version;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fecha_version;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_change;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $formulario_id;

    /**
     * @ORM\OneToOne(targetEntity="Formulario", inversedBy="estructuraDocumental")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=true)
     */
    protected $formulario;

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tabla_retencion_id;

    /**
     * @ORM\OneToOne(targetEntity="TablaRetencion", inversedBy="estructuraDocumental")
     * @ORM\JoinColumn(name="tabla_retencion_id", referencedColumnName="id", nullable=true)
     */
    protected $tablaRetencion;

    /**
     * @ORM\OneToMany(targetEntity="EstructuraDocumentalVersion", mappedBy="estructuraDocumental")
     */
    protected $listEstructuraDocumentalVersion;

    public function __construct()
    {
        $this->listEstructuraDocumentalVersion = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\EstructuraDocumental
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
     * Set the value of codigo_directorio_padre.
     *
     * @param string $codigo_directorio_padre
     * @return \App\Entity\EstructuraDocumental
     */
    public function setCodigoDirectorioPadre($codigo_directorio_padre)
    {
        $this->codigo_directorio_padre = $codigo_directorio_padre;

        return $this;
    }

    /**
     * Get the value of codigo_directorio_padre.
     *
     * @return string
     */
    public function getCodigoDirectorioPadre()
    {
        return $this->codigo_directorio_padre;
    }

    /**
     * Set the value of codigo_directorio.
     *
     * @param string $codigo_directorio
     * @return \App\Entity\EstructuraDocumental
     */
    public function setCodigoDirectorio($codigo_directorio)
    {
        if ($codigo_directorio == "") {
            $codigo_directorio = 0;
            $this->type = "tipo_documental";
        }
        $this->codigo_directorio = trim($codigo_directorio);

        return $this;
    }

    /**
     * Get the value of codigo_directorio.
     *
     * @return string
     */
    public function getCodigoDirectorio()
    {
        return $this->codigo_directorio;
    }

    /**
     * Set the value of descripcion.
     *
     * @param string $descripcion
     * @return \App\Entity\EstructuraDocumental
     */
    public function setDescripcion($descripcion)
    {

        $descripcionArray = explode("|", $descripcion);
        if (isset($descripcionArray[1])) {
            $this->descripcion = trim($descripcionArray[0]);
            $this->peso = trim($descripcionArray[1]);
        } else {
            $this->descripcion = trim($descripcion);
        }
        return $this;
    }

    /**
     * Get the value of descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        if (null !== $this->peso) {
            return $this->descripcion . "|" . $this->peso;
        }
        return $this->descripcion;
    }

    public function getDescripcionSimple()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of idestructura.
     *
     * @param string $idestructura
     * @return \App\Entity\EstructuraDocumental
     */
    public function setIdestructura($idestructura)
    {
        $this->idestructura = $idestructura;

        return $this;
    }

    /**
     * Get the value of idestructura.
     *
     * @return string
     */
    public function getIdestructura()
    {
        return $this->idestructura;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\EstructuraDocumental
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
     * Set the value of type.
     *
     * @param integer $type
     * @return \App\Entity\EstructuraDocumental
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of type.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of version.
     *
     * @param integer $version
     * @return \App\Entity\EstructuraDocumental
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
     * Set the value of has_change.
     *
     * @param boolean $has_change
     * @return \App\Entity\EstructuraDocumental
     */
    public function setHasChange($has_change)
    {
        $this->has_change = $has_change;

        return $this;
    }

    /**
     * Get the value of has_change.
     *
     * @return boolean
     */
    public function getHasChange()
    {
        return $this->has_change;
    }

    /**
     * Set the value of tabla_retencion_id.
     *
     * @param integer $tabla_retencion_id
     * @return \App\Entity\EstructuraDocumental
     */
    public function setTablaRetencionId($tabla_retencion_id)
    {
        $this->tabla_retencion_id = $tabla_retencion_id;

        return $this;
    }

    /**
     * Get the value of tabla_retencion_id.
     *
     * @return integer
     */
    public function getTablaRetencionId()
    {
        return $this->tabla_retencion_id;
    }

    /**
     * Set TablaRetencion entity (one to one).
     *
     * @param \App\Entity\TablaRetencion $tablaRetencion
     * @return \App\Entity\EstructuraDocumental
     */
    public function setTablaRetencion(TablaRetencion $tablaRetencion = null)
    {
        if ($tablaRetencion != null) {
            $tablaRetencion->setEstructuraDocumental($this);
        }
        
        $this->tablaRetencion = $tablaRetencion;

        return $this;
    }

    /**
     * Get TablaRetencion entity (one to one).
     *
     * @return \App\Entity\TablaRetencion
     */
    public function getTablaRetencion()
    {
        return $this->tablaRetencion;
    }

    /**
     * Get the value of fecha_version
     */
    public function getFechaVersion()
    {
        return $this->fecha_version;
    }

    /**
     * Set the value of fecha_version
     *
     * @return date
     */
    public function setFechaVersion($fecha_version)
    {
        $this->fecha_version = $fecha_version;

        return $this;
    }

    /**
     * Get the value of peso
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set the value of peso
     *
     * @return  self
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\EstructuraDocumental
     */
    public function setFormularioId($formularioId)
    {
        $this->formulario_id = $formularioId;

        return $this;
    }

    /**
     * Get the value of formulario_id.
     *
     * @return integer
     */
    public function getFormularioId()
    {
        return $this->formulario_id;
    }

    /**
     * Set Formulario entity (one to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\EstructuraDocumental
     */
    public function setFormulario(Formulario $formulario = null)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Get TablaRetencion entity (one to one).
     *
     * @return \App\Entity\Formulario
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    /**
     * Add EstructuraDocumentalVersion entity to collection (one to many).
     *
     * @param \App\Entity\EstructuraDocumentalVersion $EstructuraDocumentalVersion
     * @return \App\Entity\EstructuraDocumental
     */
    public function addEstructuraDocumental(EstructuraDocumentalVersion $estructuraDocumentalVersion)
    {
        $this->listEstructuraDocumentalVersion[] = $estructuraDocumentalVersion;

        return $this;
    }

    /**
     * Remove EstructuraDocumentalVersion entity from collection (one to many).
     *
     * @param \App\Entity\EstructuraDocumentalVersion $EstructuraDocumentalVersion
     * @return \App\Entity\EstructuraDocumental
     */
    public function removeEstructuraDocumental(EstructuraDocumentalVersion $estructuraDocumentalVersion)
    {
        $this->listEstructuraDocumentalVersion->removeElement($estructuraDocumentalVersion);

        return $this;
    }

    /**
     * Get FormulariosVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListEstructuraDocumentalVersion()
    {
        return $this->listEstructuraDocumentalVersion;
    }

    public function __sleep()
    {
        return array('id', 'codigo_directorio_padre', 'codigo_directorio', 'descripcion', 'idestructura', 'estado_id', 'type', 'version', 'fecha_version', 'peso');
    }
}
