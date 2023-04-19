<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\EstructuraDocumentalVersionByNode;
use App\Controller\DocumentosEstructuraDocumentalVersion;
use App\Controller\EstructuraDocumentalXlsVersion;

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
 *         "path"="/estructura_documentals_version/getestructurabynode",
 *         "controller"=EstructuraDocumentalVersionByNode::class
 *      },
 *      "getxlstrd"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/getxlstrd/{nodo}/{version}",
 *         "controller"=EstructuraDocumentalXlsVersion::class
 *      },
 *      "getnonrelated"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/nonrelated",
 *         "controller"=EstructuraDocumentalNonRelated::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *      "gettiposdocumentales"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/tiposdocumentals",
 *         "controller"=TiposDocumentales::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *      },
 *     "checkduplicatecodigodirectorio"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/codigodirectorio/{codigo_directorio}/checkduplicate/{esActualizacion}",
 *         "controller"=DuplicateCodigoDirectorio::class,
 *      },
 *     "export"={
 *          "method"="POST",
 *          "path"="/estructura_documentals_version/export",
 *          "controller"=EstructuraDocumentalExport::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *     "save"={
 *          "method"="POST",
 *          "path"="/estructura_documentals_version/save",
 *          "controller"=EstructuraDocumentalSave::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "import"={
 *         "method"="POST",
 *         "path"="/estructura_documentals_version/import",
 *         "controller"=EstructuraDocumentalImport::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "getregistrosbyestructura"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/{id}/getdocuments",
 *         "controller"=DocumentosEstructuraDocumentalVersion::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          } 
 *      },
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *     "gettipodocumentalbyid"={
 *         "method"="GET",
 *         "path"="/estructura_documentals_version/tiposdocumentals/{id}",
 *         "controller"=TipoDocumental::class,
 *      },
 *      "inactivate"={
 *          "method"="POST",
 *          "path"="/estructura_documentals_version/{id}/inactivate",
 *          "controller"=InactivateNode::class,
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
 * App\Entity\EstructuraDocumentalVersion
 *
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="App\Repository\EstructuraDocumentalVersionRepository")
 * @ORM\Table(name="estructura_documental_version")
 */
class EstructuraDocumentalVersion
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
    protected $codigo_directorio_padre;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $codigo_directorio;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
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
     * @ORM\OneToOne(targetEntity="TablaRetencionVersion", mappedBy="estructuraDocumentalVersion")
     */
    protected $tablaRetencionVersion;

     /**
     * @ORM\OneToOne(targetEntity="Formulario")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=true)
     */
    /**
     * @ORM\ManyToOne(targetEntity="Formulario")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=true)
     */
    protected $formulario;

    /**
     * @ORM\OneToMany(targetEntity="FormularioVersion", mappedBy="estructuraDocumentalVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="estructura_documental_version_id", nullable=false)
     */
    protected $formulariosVersion;

    /**
     * @ORM\ManyToOne(targetEntity="EstructuraDocumental", inversedBy="listEstructuraDocumentalVersion")
     * @ORM\JoinColumn(name="estructura_documental_id", referencedColumnName="id", nullable=true)
     */
    protected $estructuraDocumental;

    public function __construct()
    {
        $this->tablaRetencionVersions = new ArrayCollection();
        $this->formulariosVersion = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * Set the value of estructuraDocumental.
     *
     * @param EstructuraDocumental $estructuraDocumental
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function setEstructuraDocumental(EstructuraDocumental $estructuraDocumental)
    {
        $this->estructuraDocumental = $estructuraDocumental;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return EstructuraDocumental
     */
    public function getEstructuraDocumental()
    {
        return $this->estructuraDocumental;
    }

    /**
     * Set the value of codigo_directorio_padre.
     *
     * @param string $codigo_directorio_padre
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function setCodigoDirectorio($codigo_directorio)
    {
        /*if ($codigo_directorio == "") {
            $codigo_directorio = 0;
            $this->type = "tipo_documental";
        }*/
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
     * @return \App\Entity\EstructuraDocumentalVersion
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
        $descripcionArray = explode("|", $this->descripcion);
        if (isset($descripcionArray[1])) {
            $descripcion = trim($descripcionArray[0]);
        } else {
            $descripcion = trim($this->descripcion);
        }
        return trim($descripcion);
    }

    /**
     * Set the value of idestructura.
     *
     * @param string $idestructura
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * Set TablaRetencionVersion entity (one to one).
     *
     * @param \App\Entity\TablaRetencionVersion $tablaRetencion
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function setTablaRetencionVersion(TablaRetencionVersion $tablaRetencionVersion)
    {
        $tablaRetencionVersion->setEstructuraDocumentalVersion($this);
        $this->tablaRetencionVersion = $tablaRetencionVersion;

        return $this;
    }

    /**
     * Get TablaRetencionVersion entity (one to one).
     *
     * @return \App\Entity\TablaRetencionVersion
     */
    public function getTablaRetencionVersion()
    {
        return $this->tablaRetencionVersion;
    }

    public function __sleep()
    {
        return array('id', 'estructura_documental_version_id', 'codigo_directorio_padre', 'codigo_directorio', 'descripcion', 'idestructura', 'estado_id', 'type', 'version');
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
     * @return  self
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
     * Set Formulario entity (one to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\EstructuraDocumentalVersion
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
     * Add FormularioVersion entity to collection (one to many).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function addFormularioVersion(FormularioVersion $formularioVersion)
    {
        $this->formulariosVersion[] = $formularioVersion;

        return $this;
    }

    /**
     * Remove FormularioVersion entity from collection (one to many).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function removeFormularioVersion(FormularioVersion $formularioVersion)
    {
        $this->formulariosVersion->removeElement($formularioVersion);

        return $this;
    }

    /**
     * Get FormulariosVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormulariosVersion()
    {
        return $this->formulariosVersion;
    }
}
