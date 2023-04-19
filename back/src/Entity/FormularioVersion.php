<?php
//TODO: Verificar inactivar formulario

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\CamposFormularioBasic;
use App\Controller\CamposFormularioRespuesta;
use App\Controller\DuplicateForm;
use App\Controller\FormsVersionByUser;
use App\Controller\FormsVersionByUserSearch;
use App\Controller\FormsOption;
use App\Controller\GenerarEstructuraModeloVersion;
use App\Controller\GenerateFormVersion;
use App\Controller\InactivateForm;
use App\Controller\ProcesarEstructuraModeloVersion;
use App\Controller\FormVersionFieldLoader;
use App\Controller\RelatedFormVersion;
use App\Controller\FormularioVersion\FormularioVersionPorFlujoTrabajo;
use App\Controller\FormularioVersion\FormularioVersionPorRegistro;

use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"},
 *      "procesarestructuramodelo"={
 *          "method"="POST",
 *          "path"="/formulariosVersion/{id}/procesarestructuramodelo",
 *          "controller"=ProcesarEstructuraModeloVersion::class,
 *          "defaults"={"_api_receive"=false}
 *          },
 *      "generarestructuramodelo"={
 *          "method"="POST",
 *          "path"="/formulariosVersion/{id}/generarestructuramodelo",
 *          "controller"=GenerarEstructuraModeloVersion::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "formsbyuser"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/byuser",
 *          "controller"=FormsVersionByUser::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "formsbyuserSearch"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/byuserSearch",
 *          "controller"=FormsVersionByUserSearch::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "camposformulario"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/{id}/load_campos_formulario",
 *          "controller"=FormVersionFieldLoader::class
 *      },
 *      "formulariosrelacionados"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/{id}/relacionados",
 *          "controller"=RelatedFormVersion::class
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/formulariosVersion/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "getCamposFormularioBasic"={
 *         "method"="GET",
 *         "path"="/formulariosVersion/{id}/campo_formularios/basic",
 *         "controller"=CamposFormularioBasic::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *        },
 *      "duplicate"={
 *          "method"="POST",
 *          "path"="/formulariosVersion/{id}/duplicate",
 *          "controller"=DuplicateForm::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "generateversion"={
 *          "method"="POST",
 *          "path"="/formulariosVersion/{id}/generateversion",
 *          "controller"=GenerateFormVersion::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "opcionesForm"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/{id}/opciones",
 *          "controller"=FormsOption::class,
 *          "requirements"={"id"="\d+"}
 *      },
 *      "inactivate"={
 *          "method"="POST",
 *          "path"="/formulariosVersion/{id}/inactivate",
 *          "controller"=InactivateForm::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "camposformulario"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/{id}/campos_form/{registro_id}",
 *          "controller"=CamposFormularioRespuesta::class
 *      },
 *      "por_flujo_trabajo_version"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/{id}/flujo_trabajo_version",
 *          "controller"=FormularioVersionPorFlujoTrabajo::class
 *      },
 *      "por_registro_id"={
 *          "method"="GET",
 *          "path"="/formulariosVersion/{id}/registro",
 *          "controller"=FormularioVersionPorRegistro::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *  }
 * )
 *
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"version","fecha_version","nombre","inicio_vigencia","fin_vigencia","ayuda"}
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"tipo_formulario": "exact"})
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"version","fecha_version","nombre","inicio_vigencia","fin_vigencia","ayuda"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\FormularioVersion
 *
 * @ORM\Entity(repositoryClass="App\Repository\FormularioVersionRepository")
 * @ORM\Table(name="formulario_version")
 */
class FormularioVersion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tipo_formulario;

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
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $nomenclatura_formulario;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $formulario_transversal;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $permite_tareas;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $genera_pdf_con_firma_digital;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $radicado_electronico;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $tipo_sticker;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inicio_vigencia;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $fin_vigencia;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $ayuda;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $tabla_retencion_disposicion_final_conservacion_digital;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estructura_documental_version_id;

    /**
     * @ORM\OneToMany(targetEntity="CampoFormularioVersion", mappedBy="formularioVersion", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_version_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $campoFormulariosVersion;

    /**
     * @ORM\OneToMany(targetEntity="OpcionFormularioVersion", mappedBy="formularioVersion", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_version_id", nullable=false)
     */
    protected $opcionFormulariosVersion;

    /**
     * @ORM\OneToMany(targetEntity="PlantillaVersion", mappedBy="formularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_version_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $plantillasVersion;

    /**
     * @ORM\OneToMany(targetEntity="Registro", mappedBy="formularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_version_id", nullable=true)
     */
    protected $registros;

    /**
     * @ORM\ManyToOne(targetEntity="EstructuraDocumentalVersion", inversedBy="formulariosVersion")
     * @ORM\JoinColumn(name="estructura_documental_version_id", referencedColumnName="id", nullable=true)
     */
    protected $estructuraDocumentalVersion;

    /**
     * @ORM\Column(type="integer")
     */
    protected $formulario_id;

    /**
     * @ORM\ManyToOne(targetEntity="Formulario")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=true)
     */
    protected $formulario;

    public function __construct()
    {
        $this->opcionFormulariosVersion = new ArrayCollection();
        $this->campoFormulariosVersion = new ArrayCollection();
        $this->plantillasVersion = new ArrayCollection();
        $this->registros = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Formulario
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
     * Set the value of tipo_formulario.
     *
     * @param integer $tipo_formulario
     * @return \App\Entity\FormularioVersion
     */
    public function setTipoFormulario($tipo_formulario)
    {
        $this->tipo_formulario = $tipo_formulario;

        return $this;
    }

    /**
     * Get the value of tipo_formulario.
     *
     * @return integer
     */
    public function getTipoFormulario()
    {
        return $this->tipo_formulario;
    }

    /**
     * Set the value of version.
     *
     * @param integer $version
     * @return \App\Entity\FormularioVersion
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
     * @return \App\Entity\FormularioVersion
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
     * Set the value of nombre.
     *
     * @param string $nombre
     * @return \App\Entity\FormularioVersion
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
     * Set the value of nomenclatura_formulario.
     *
     * @param string $nomenclatura_formulario
     * @return \App\Entity\FormularioVersion
     */
    public function setNomenclaturaFormulario($nomenclatura_formulario)
    {
        $this->nomenclatura_formulario = $nomenclatura_formulario;

        return $this;
    }

    /**
     * Get the value of nomenclatura_formulario.
     *
     * @return string
     */
    public function getNomenclaturaFormulario()
    {
        return $this->nomenclatura_formulario;
    }

    /**
     * Set the value of formulario_transversal.
     *
     * @param boolean $formulario_transversal
     * @return \App\Entity\FormularioVersion
     */
    public function setFormularioTransversal($formulario_transversal)
    {
        $this->formulario_transversal = $formulario_transversal;

        return $this;
    }

    /**
     * Get the value of formulario_transversal.
     *
     * @return boolean
     */
    public function getFormularioTransversal()
    {
        return $this->formulario_transversal;
    }

    /**
     * Set the value of permite_tareas.
     *
     * @param boolean $permite_tareas
     * @return \App\Entity\FormularioVersion
     */
    public function setPermiteTareas($permite_tareas)
    {
        $this->permite_tareas = $permite_tareas;

        return $this;
    }

    /**
     * Get the value of permite_tareas.
     *
     * @return boolean
     */
    public function getPermiteTareas()
    {
        return $this->permite_tareas;
    }

    /**
     * Set the value of genera_pdf_con_firma_digital.
     *
     * @param boolean $genera_pdf_con_firma_digital
     * @return \App\Entity\FormularioVersion
     */
    public function setGeneraPdfConFirmaDigital($genera_pdf_con_firma_digital)
    {
        $this->genera_pdf_con_firma_digital = $genera_pdf_con_firma_digital;

        return $this;
    }

    /**
     * Get the value of genera_pdf_con_firma_digital.
     *
     * @return boolean
     */
    public function getGeneraPdfConFirmaDigital()
    {
        return $this->genera_pdf_con_firma_digital;
    }

    /**
     * Set the value of radicado_electronico.
     *
     * @param boolean $radicado_electronico
     * @return \App\Entity\FormularioVersion
     */
    public function setRadicadoElectronico($radicado_electronico)
    {
        $this->radicado_electronico = $radicado_electronico;

        return $this;
    }

    /**
     * Get the value of radicado_electronico.
     *
     * @return boolean
     */
    public function getRadicadoElectronico()
    {
        return $this->radicado_electronico;
    }

    /**
     * Set the value of tipo_sticker.
     *
     * @param string $tipo_sticker
     * @return \App\Entity\FormularioVersion
     */
    public function setTipoSticker($tipo_sticker)
    {
        $this->tipo_sticker = $tipo_sticker;

        return $this;
    }

    /**
     * Get the value of tipo_sticker.
     *
     * @return string
     */
    public function getTipoSticker()
    {
        return $this->tipo_sticker;
    }

    /**
     * Set the value of inicioVigencia.
     *
     * @param \DateTime $inicioVigencia
     * @return \App\Entity\FormularioVersion
     */
    public function setInicioVigencia($inicio_vigencia)
    {
        $this->inicio_vigencia = $inicio_vigencia;

        return $this;
    }

    /**
     * Get the value of inicio_vigencia.
     *
     * @return \DateTime
     */
    public function getInicioVigencia()
    {
        return $this->inicio_vigencia;
    }

    /**
     * Set the value of fin_vigencia.
     *
     * @param \DateTime $fin_vigencia
     * @return \App\Entity\FormularioVersion
     */
    public function setFinVigencia($fin_vigencia)
    {
        $this->fin_vigencia = $fin_vigencia;

        return $this;
    }

    /**
     * Get the value of finVigencia.
     *
     * @return \DateTime
     */
    public function getFinVigencia()
    {
        return $this->fin_vigencia;
    }

    /**
     * Set the value of ayuda.
     *
     * @param string $ayuda
     * @return \App\Entity\FormularioVersion
     */
    public function setAyuda($ayuda)
    {
        $this->ayuda = $ayuda;

        return $this;
    }

    /**
     * Get the value of ayuda.
     *
     * @return string
     */
    public function getAyuda()
    {
        return $this->ayuda;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\FormularioVersion
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
     * Set the value of tabla_retencion_disposicion_final_conservacion_digital.
     *
     * @param boolean $tabla_retencion_disposicion_final_conservacion_digital
     * @return \App\Entity\FormularioVersion
     */
    public function setTablaRetencionDisposicionFinalConservacionDigital($tabla_retencion_disposicion_final_conservacion_digital)
    {
        $this->tabla_retencion_disposicion_final_conservacion_digital = $tabla_retencion_disposicion_final_conservacion_digital;

        return $this;
    }

    /**
     * Get the value of tabla_retencion_disposicion_final_conservacion_digital.
     *
     * @return boolean
     */
    public function getTablaRetencionDisposicionFinalConservacionDigital()
    {
        return $this->tabla_retencion_disposicion_final_conservacion_digital;
    }

    /**
     * Set the value of estructura_documental_version_id.
     *
     * @param integer $estructura_documental_version_id
     * @return \App\Entity\FormularioVersion
     */
    public function setEstructuraDocumentalVersionId($estructura_documental_version_id)
    {
        $this->estructura_documental_version_id = $estructura_documental_version_id;

        return $this;
    }

    /**
     * Get the value of estructura_documental_version_id.
     *
     * @return integer
     */
    public function getEstructuraDocumentalVersionId()
    {
        return $this->estructura_documental_version_id;
    }

    /**
     * Add CampoFormulario entity to collection (one to many).
     *
     * @param \App\Entity\CampoFormularioVersion $campoFormularioVersion
     * @return \App\Entity\FormularioVersion
     */
    public function addCampoFormularioVersion(CampoFormularioVersion $campoFormularioVersion)
    {
        $this->campoFormulariosVersion[] = $campoFormularioVersion;

        return $this;
    }

    /**
     * Remove CampoFormulario entity from collection (one to many).
     *
     * @param \App\Entity\CampoFormularioVersion $campoFormularioVersion
     * @return \App\Entity\FormularioVersion
     */
    public function removeCampoFormularioVersion(CampoFormularioVersion $campoFormularioVersion)
    {
        $this->campoFormulariosVersion->removeElement($campoFormularioVersion);

        return $this;
    }

    /**
     * Get CampoFormulario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCampoFormulariosVersion()
    {
        return $this->campoFormulariosVersion;
    }

    /**
     * Add OpcionFormulario entity to collection (one to many).
     *
     * @param \App\Entity\OpcionFormularioVersion $opcionFormularioVersion
     * @return \App\Entity\FormularioVersion
     */
    public function addOpcionFormularioVersion(OpcionFormularioVersion $opcionFormularioVersion)
    {
        $this->opcionFormulariosVersion[] = $opcionFormularioVersion;

        return $this;
    }

    /**
     * Remove OpcionFormularioVersion entity from collection (one to many).
     *
     * @param \App\Entity\OpcionFormularioVersion $opcionFormularioVersion
     * @return \App\Entity\FormularioVersion
     */
    public function removeOpcionFormularioVersion(OpcionFormularioVersion $opcionFormularioVersion)
    {
        $this->opcionFormulariosVersion->removeElement($opcionFormularioVersion);

        return $this;
    }

    /**
     * Get OpcionFormulario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpcionFormulariosVersion()
    {
        return $this->opcionFormulariosVersion;
    }

    /**
     * Add PlantillaVersion entity to collection (one to many).
     *
     * @param \App\Entity\PlantillaVersion $plantillaVersion
     * @return \App\Entity\FormularioVersion
     */
    public function addPlantillaVersion(PlantillaVersion $plantillaVersion)
    {
        $this->plantillasVersion[] = $plantillaVersion;

        return $this;
    }

    /**
     * Remove PlantillaVersion entity from collection (one to many).
     *
     * @param \App\Entity\PlantillaVersion $plantillaVersion
     * @return \App\Entity\FormularioVersion
     */
    public function removePlantillaVersion(PlantillaVersion $plantillaVersion)
    {
        $this->plantillasVersion->removeElement($plantillaVersion);

        return $this;
    }

    /**
     * Get PlantillaVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlantillasVersion()
    {
        return $this->plantillasVersion;
    }

    /**
     * Add Registro entity to collection (one to many).
     *
     * @param \App\Entity\Registro $registro
     * @return \App\Entity\FormularioVersion
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
     * @return \App\Entity\FormularioVersion
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
     * Set Registros entity collection (one to many).
     *
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setRegistros(ArrayCollection $registros)
    {
        $this->registros = $registros;

        return $this;
    }

    /**
     * Set Formulario entity (one to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\FormularioVersion
     */
    public function setFormulario(Formulario $formulario = null)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Get Formulario entity 
     *
     * @return \App\Entity\Formulario
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $this->setEstructuraDocumentalVersion(null);
            $this->setVersion(1);
            
            //se clonan los campos relacionados al formulario
            $campoFormulariosVersion = $this->getCampoFormulariosVersion();
            $campoFormulariosArray = new ArrayCollection();
            foreach ($campoFormulariosVersion as $campoFormularioVersion) {
                $campoFormularioVersionClone = clone $campoFormularioVersion;
                $campoFormularioVersionClone->setFormularioVersion($this);
                $campoFormulariosArray->add($campoFormularioVersionClone);
            }
            $this->campoFormulariosVersion = $campoFormulariosArray;
            
            //se clonan las opciones relacionadas al formulario
            $opcionFormulariosVersion = $this->getOpcionFormulariosVersion();
            $opcionFormulariosArray = new ArrayCollection();
            foreach ($opcionFormulariosVersion as $opcionFormularioVersion) {
                $opcionFormularioClone = clone $opcionFormularioVersion;
                $opcionFormularioClone->setFormularioVersion($this);
                $opcionFormularioClone->setOpcionFormulario($opcionFormularioVersion->getOpcionFormulario());
                $opcionFormulariosArray->add($opcionFormularioClone);
            }
            $this->opcionFormulariosVersion = $opcionFormulariosArray;
        }
    }

    /**
     * Set EstructuraDocumentalVersion entity (many to one).
     *
     * @param \App\Entity\EstructuraDocumentalVersion $estructuraDocumentalVersion
     * @return \App\Entity\FormularioVersion
     */
    public function setEstructuraDocumentalVersion(EstructuraDocumentalVersion $estructuraDocumentalVersion = null)
    {
        $this->estructuraDocumentalVersion = $estructuraDocumentalVersion;
        return $this;
    }

    /**
     * Get EstructuraDocumentalVersion entity (many to one).
     *
     * @return \App\Entity\EstructuraDocumentalVersion
     */
    public function getEstructuraDocumentalVersion()
    {
        return $this->estructuraDocumentalVersion;
    }

    /**
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\FormularioVersion
     */
    public function setFormularioId($formulario_id)
    {
        $this->formulario_id = $formulario_id;

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

    public function __sleep()
    {
        return array('id', 'tipo_formulario', 'version', 'fecha_version', 'nombre', 'formulario_padre', 'nomenclatura_formulario', 'formulario_transversal', 'permite_tareas', 'genera_pdf_con_firma_digital', 'radicado_electronico', 'tipo_sticker', 'inicioVigencia', 'finVigencia', 'ayuda', 'estado_id', 'tabla_retencion_disposicion_final_conservacion_digital', 'estructura_documental_version_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'ayuda' => $this->getAyuda(),
            'nombre' => $this->getNombre(),
            'version' => $this->getVersion(),
            'inicio_vigencia' => $this->getInicioVigencia(),
            'fin_vigencia' => $this->getFinVigencia(),
        ];
    }
}