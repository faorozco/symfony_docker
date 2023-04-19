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
use App\Controller\FormSave;
use App\Controller\Formulario\FormCreate;
use App\Controller\Formulario\FormList;
use App\Controller\FormsByUser;
use App\Controller\FormsOption;
use App\Controller\GenerarEstructuraModelo;
use App\Controller\GenerateFormVersion;
use App\Controller\InactivateForm;
use App\Controller\ActivateForm;
use App\Controller\ProcesarEstructuraModelo;
use App\Controller\FormFieldLoader;
use App\Controller\FormNotRelatedWithDocumentalEstructure;
use App\Controller\FormAssociatedWithDocumentalEstructure;
use App\Controller\RelatedForm;
use App\Controller\FormByDocumentalEstructureId;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "formCreate"={"method"="POST",
 *          "path"="/formularios/create",
 *          "controller"=FormCreate::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "formSave"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/save",
 *          "controller"=FormSave::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "procesarestructuramodelo"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/procesarestructuramodelo",
 *          "controller"=ProcesarEstructuraModelo::class,
 *          "defaults"={"_api_receive"=false}
 *          },
 *      "generarestructuramodelo"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/generarestructuramodelo",
 *          "controller"=GenerarEstructuraModelo::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "formsbyuser"={
 *          "method"="GET",
 *          "path"="/formularios/byuser",
 *          "controller"=FormsByUser::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "camposformulario"={
 *          "method"="GET",
 *          "path"="/formularios/{id}/load_campos_formulario",
 *          "controller"=FormFieldLoader::class
 *      },
 *      "formulariosrelacionados"={
 *          "method"="GET",
 *          "path"="/formularios/{id}/relacionados",
 *          "controller"=RelatedForm::class
 *      },
 *      "formulariosNoRelacionados"={
 *          "method"="GET",
 *          "path"="/formularios/notRelatedWithDocumentalEstructure",
 *          "controller"=FormNotRelatedWithDocumentalEstructure::class
 *      },
 *      "formularioAsociado"={
 *          "method"="GET",
 *          "path"="/formularios/{estructuraDocumentalId}/associated",
 *          "controller"=FormAssociatedWithDocumentalEstructure::class
 *      }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/formularios/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *      "getFormulariosByEstructuradocumentalId"={
 *         "method"="GET",
 *         "path"="/formularios/{id}/estructuraDocumentalId",
 *         "controller"=FormByDocumentalEstructureId::class,
 *         "defaults"={"_api_receive"=false}
 *        },
 *      "getCamposFormularioBasic"={
 *         "method"="GET",
 *         "path"="/formularios/{id}/campo_formularios/basic",
 *         "controller"=CamposFormularioBasic::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *        },
 *      "getforumlarios"={
 *         "method"="GET",
 *         "path"="/formularios/list",
 *         "controller"=FormList::class,
 *         "defaults"={"_api_receive"=false}
 *      },
 *      "duplicate"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/duplicate",
 *          "controller"=DuplicateForm::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "generateversion"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/generateversion",
 *          "controller"=GenerateFormVersion::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "opcionesForm"={
 *          "method"="GET",
 *          "path"="/formularios/{id}/opciones",
 *          "controller"=FormsOption::class,
 *          "requirements"={"id"="\d+"}
 *      },
 *      "inactivate"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/inactivate",
 *          "controller"=InactivateForm::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "activate"={
 *          "method"="POST",
 *          "path"="/formularios/{id}/activate",
 *          "controller"=ActivateForm::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "camposformulario"={
 *          "method"="GET",
 *          "path"="/formularios/{id}/campos_form/{registro_id}",
 *          "controller"=CamposFormularioRespuesta::class
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
 * App\Entity\Formulario
 *
 * @ORM\Entity(repositoryClass="App\Repository\FormularioRepository")
 * @ORM\Table(name="formulario")
 */
class Formulario
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
    protected $flujo_trabajo_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */

    protected $estructura_documental_id;

    /**
     * @ORM\OneToMany(targetEntity="CampoFormulario", mappedBy="formulario", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $campoFormularios;

    /**
     * @ORM\OneToMany(targetEntity="OpcionFormulario", mappedBy="formulario", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_id", nullable=false)
     */
    protected $opcionFormularios;

    /**
     * @ORM\OneToMany(targetEntity="ConsultaMaestra", mappedBy="formulario")
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_id", nullable=false)
     */
    protected $consultaMaestras;

    /**
     * @ORM\ManyToMany(targetEntity="Grupo", inversedBy="formularios", cascade={"persist","remove"})
     * @ORM\JoinTable(
     *  name="formulario_grupo",
     *  joinColumns={
     *      @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", onDelete="CASCADE")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="grupo_id", referencedColumnName="id", onDelete="CASCADE")
     *  }
     * )
     * @ApiSubresource(maxDepth=1)
     */

    protected $grupos;

    /**
     * @ORM\OneToMany(targetEntity="Plantilla", mappedBy="formulario")
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $plantillas;

    /**
     * @ORM\OneToMany(targetEntity="FlujoTrabajo", mappedBy="formulario")
     * @ORM\JoinColumn(name="id", referencedColumnName="formulario_id", nullable=true)
     */
    protected $flujoTrabajos;

    /**
     * @ORM\OneToOne(targetEntity="EstructuraDocumental", inversedBy="formulario")
     * @ORM\JoinColumn(name="estructura_documental_id", referencedColumnName="id", nullable=true)
     */
    protected $estructuraDocumental;

    public function __construct()
    {
        $this->opcionFormularios = new ArrayCollection();
        $this->campoFormularios = new ArrayCollection();
        $this->consultaMaestras = new ArrayCollection();
        $this->flujoTrabajos = new ArrayCollection();
        $this->grupos = new ArrayCollection();
        $this->plantillas = new ArrayCollection();
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
 * Get the value of flujo_trabajo_id
 */
    public function getFlujoTrabajoId()
    {
        return $this->flujo_trabajo_id;
    }

    /**
     * Set the value of flujo_trabajo_id
     *
     * @return  self
     */
    public function setFlujoTrabajoId($flujo_trabajo_id)
    {
        $this->flujo_trabajo_id = $flujo_trabajo_id;

        return $this;
    }

    /**
     * Set the value of inicioVigencia.
     *
     * @param \DateTime $inicioVigencia
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * Add CampoFormulario entity to collection (one to many).
     *
     * @param \App\Entity\CampoFormulario $campoFormulario
     * @return \App\Entity\Formulario
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
     * @return \App\Entity\Formulario
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
     * Add OpcionFormulario entity to collection (one to many).
     *
     * @param \App\Entity\OpcionFormulario $opcionFormulario
     * @return \App\Entity\Formulario
     */
    public function addOpcionFormulario(OpcionFormulario $opcionFormulario)
    {
        $this->opcionFormularios[] = $opcionFormulario;

        return $this;
    }

    /**
     * Remove OpcionFormulario entity from collection (one to many).
     *
     * @param \App\Entity\OpcionFormulario $opcionFormulario
     * @return \App\Entity\Formulario
     */
    public function removeOpcionFormulario(OpcionFormulario $opcionFormulario)
    {
        $this->opcionFormularios->removeElement($opcionFormulario);

        return $this;
    }

    /**
     * Get OpcionFormulario entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpcionFormularios()
    {
        return $this->opcionFormularios;
    }

    /**
     * Add ConsultaMaestra entity to collection (one to many).
     *
     * @param \App\Entity\ConsultaMaestra $consultaMaestra
     * @return \App\Entity\Formulario
     */
    public function addConsultaMaestra(ConsultaMaestra $consultaMaestra)
    {
        $this->consultaMaestras[] = $consultaMaestra;

        return $this;
    }

    /**
     * Remove ConsultaMaestra entity from collection (one to many).
     *
     * @param \App\Entity\ConsultaMaestra $consultaMaestra
     * @return \App\Entity\Formulario
     */
    public function removeConsultaMaestra(ConsultaMaestra $consultaMaestra)
    {
        $this->consultaMaestras->removeElement($consultaMaestra);

        return $this;
    }

    /**
     * Get ConsultaMaestra entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsultaMaestras()
    {
        return $this->consultaMaestras;
    }

 /**
     * Add FlujoTrabajo entity to collection (one to many).
     *
     * @param \App\Entity\FlujoTrabajo $flujoTrabajo
     * @return \App\Entity\Formulario
     */
    public function addFlujoTrabajo(FlujoTrabajo $flujoTrabajo)
    {
        $this->flujoTrabajos[] = $flujoTrabajo;

        return $this;
    }

    /**
     * Remove FlujoTrabajo entity from collection (one to many).
     *
     * @param \App\Entity\FlujoTrabajo $flujoTrabajo
     * @return \App\Entity\Formulario
     */
    public function removeFlujoTrabajo(FlujoTrabajo $flujoTrabajo)
    {
        $this->flujoTrabajos->removeElement($flujoTrabajo);

        return $this;
    }

    /**
     * Get FlujoTrabajo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlujoTrabajos()
    {
        return $this->flujoTrabajos;
    }

    /**
     * Add Grupo entity to collection (one to many).
     *
     * @param \App\Entity\Grupo $formulario
     * @return \App\Entity\Formulario
     */
    public function addGrupo(Grupo $formulario)
    {
        $this->grupos[] = $formulario;

        return $this;
    }

    /**
     * Remove Grupo entity from collection (one to many).
     *
     * @param \App\Entity\Grupo $formulario
     * @return \App\Entity\Formulario
     */
    public function removeGrupo(Grupo $grupo)
    {
        $this->grupos->removeElement($grupo);

        return $this;
    }

    /**
     * Get Grupo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * Get Grupo entity collection (one to many).
     *
     * @return \App\Entity\Formulario
     */
    public function clearGrupo()
    {
        $this->grupos = new ArrayCollection();
        return $this;
    }

    /**
     * Add Plantilla entity to collection (one to many).
     *
     * @param \App\Entity\Plantilla $plantilla
     * @return \App\Entity\Formulario
     */
    public function addPlantilla(Plantilla $plantilla)
    {
        $this->plantillas[] = $plantilla;

        return $this;
    }

    /**
     * Remove Plantilla entity from collection (one to many).
     *
     * @param \App\Entity\Plantilla $plantilla
     * @return \App\Entity\Formulario
     */
    public function removePlantilla(Plantilla $plantilla)
    {
        $this->plantillas->removeElement($plantilla);

        return $this;
    }

    /**
     * Get Plantilla entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlantillas()
    {
        return $this->plantillas;
    }

    /**
     * Set EstructuraDocumental entity (one to one).
     *
     * @param \App\Entity\EstructuraDocumental $estructuraDocumental
     * @return \App\Entity\Formulario
     */
    public function setEstructuraDocumental(EstructuraDocumental $estructuraDocumental = null)
    {
        $this->estructuraDocumental = $estructuraDocumental;

        return $this;
    }

    /**
     * Get EstructuraDocumental entity (one to one).
     *
     * @return \App\Entity\EstructuraDocumental
     */
    public function getEstructuraDocumental()
    {
        return $this->estructuraDocumental;
    }

    /**
     * Set the value of estructura_documental_id.
     *
     * @param integer $estructura_documental_id
     * @return \App\Entity\Formulario
     */
    public function setEstructuraDocumentalId($estructura_documental_id)
    {
        $this->estructura_documental_id = $estructura_documental_id;

        return $this;
    }

    /**
     * Get the value of estructura_documental_id.
     *
     * @return integer
     */
    public function getEstructuraDocumentalId()
    {
        return $this->estructura_documental_id;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $this->setVersion(1);
            
            //se clonan los campos relacionados al formulario
            $campoFormularios = $this->getCampoFormularios();
            $campoFormulariosArray = new ArrayCollection();
            foreach ($campoFormularios as $campoFormulario) {
                $campoFormularioClone = clone $campoFormulario;
                $campoFormularioClone->setFormulario($this);
                $campoFormulariosArray->add($campoFormularioClone);
            }
            $this->campoFormularios = $campoFormulariosArray;
            
            //se clonan las opciones relacionadas al formulario
            $opcionFormularios = $this->getOpcionFormularios();
            $opcionFormulariosArray = new ArrayCollection();
            foreach ($opcionFormularios as $opcionFormulario) {
                $opcionFormularioClone = clone $opcionFormulario;
                $opcionFormularioClone->setFormulario($this);
                $opcionFormulariosArray->add($opcionFormularioClone);
            }
            $this->opcionFormularios = $opcionFormulariosArray;
        }
    }

    public function __sleep()
    {
        return array('id', 'tipo_formulario', 'version', 'fecha_version', 'nombre', 'formulario_padre', 'nomenclatura_formulario', 'formulario_transversal', 'permite_tareas', 'genera_pdf_con_firma_digital', 'radicado_electronico', 'tipo_sticker', 'inicioVigencia', 'finVigencia', 'ayuda', 'estado_id', 'tabla_retencion_disposicion_final_conservacion_digital');
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