<?php
//TODO: Validar los campos al lado del cliente, si viene NULL que no los envie.(OCurre con  usar front  e imprime sticker)


namespace App\Entity;

use App\Utils\TextUtils;
use App\Filter\ORSearchFilter;
use App\Controller\EntityListerVersion;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\FormFieldListerVersion;
use App\Controller\CampoFormularioUpdate;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Controller\CamposFormularioUpdate;
use Doctrine\ORM\Mapping\UniqueConstraint;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *    attributes={"pagination_items_per_page"=100},
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "put"={
 *          "method"="PUT",
 *          "path"="/campo_formularios_version/{id}",
 *          "controller"=CampoFormularioVersionUpdate::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "actualizarRegistro"={
 *          "method"="PUT",
 *          "path"="/update_campo_formularios_version",
 *          "controller"=CamposFormularioUpdate::class,
 *          "defaults"={"_api_receive"=false}
 *          },
 *      "get"={
 *         "method"="GET",
 *         "path"="/campo_formularios_version/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *       "getEspecial"={
 *         "method"="GET",
 *         "path"="/campo_formularios_version/{id}/listFields",
 *         "controller"=EntityListerVersion::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *        },
 *       "getFormFieldsValues"={
 *         "method"="GET",
 *         "path"="/campo_formularios_version/{id}/getformfieldsvalues",
 *         "controller"=FormFieldListerVersion::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *        },
 *   }
 * )
 *
 *  @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"id","campo", "valor_cuadro_texto", "ayuda", "posicion"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"id", "campo", "valor_cuadro_texto", "ayuda", "posicion"},
 *      arguments={"orderParameterName"="order"})
 *
 *
 * @UniqueEntity(
 *     fields={"formularioVersion", "valor_cuadro_texto", "estado_id"},
 *     errorPath="valor_cuadro_texto",
 *     message="Esta etiqueta ya esta siendo usada en este formulario."
 * )
 * App\Entity\CampoFormularioVersionVersion
 *
 * @ORM\Entity(repositoryClass="App\Repository\CampoFormularioVersionRepository")
 * @ORM\Table(name="campo_formulario_version",
 *          indexes={
 *              @ORM\Index(
 *                  name="fk_campo_formulario_version_formulario_version1_idx",
 *                  columns={"formulario_version_id"}
 *              ),
 *              @ORM\Index(
 *                  name="fk_campo_formulario_version_Lista1_idx",
 *                  columns={"lista_id"}
 *              ),
 *              @ORM\Index(
 *                  name="fk_campo_formulario_version_entidad1_idx",
 *                  columns={"entidad_id"}
 *              )
 *          }
 * )
 */
class CampoFormularioVersion
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
    protected $campo;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $tipo_campo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $valor_cuadro_texto;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $posicion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $valor_minimo;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $longitud;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $obligatorio;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $indice;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $imprime_sticker;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $posicion_sticker;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $ayuda;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $item_tabla_defecto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $valor_defecto;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $item_lista_defecto;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $mostrar_front;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $posicion_front;

    /**
     * @ORM\Column(type="integer")
     */
    protected $formulario_version_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lista_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $entidad_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $entidad_column_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $campo_formulario_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $campo_unico;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $oculto_al_radicar;

    /**
     * @ORM\OneToMany(targetEntity="PasoCampoVersion", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $pasoCamposVersion;

    /**
     * @ORM\OneToMany(targetEntity="RegistroCampo", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroCampos;

    /**
     * @ORM\OneToMany(targetEntity="RegistroEntidad", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroEntidads;

    /**
     * @ORM\OneToMany(targetEntity="RegistroFecha", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroFechas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroHora", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroHoras;

    /**
     * @ORM\OneToMany(targetEntity="RegistroLista", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroListas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroMultiseleccion", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroMultiseleccions;

    /**
     * @ORM\OneToMany(targetEntity="RegistroNumericoDecimal", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroNumericoDecimals;

    /**
     * @ORM\OneToMany(targetEntity="RegistroNumericoEntero", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroNumericoEnteros;

    /**
     * @ORM\OneToMany(targetEntity="RegistroNumericoMoneda", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroNumericoMonedas;

    /**
     * @ORM\OneToMany(targetEntity="RegistroTextoCorto", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroTextoCortos;

    /**
     * @ORM\OneToMany(targetEntity="RegistroTextoLargo", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroTextoLargos;

    /**
     * @ORM\OneToMany(targetEntity="RegistroBooleano", mappedBy="campoFormularioVersion")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_version_id", nullable=false)
     */
    protected $registroBooleanos;

    /**
     * @ORM\ManyToOne(targetEntity="FormularioVersion", inversedBy="campoFormulariosVersion")
     * @ORM\JoinColumn(name="formulario_version_id", referencedColumnName="id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $formularioVersion;

    /**
     * @ORM\ManyToOne(targetEntity="Lista", inversedBy="campoFormulariosVersion")
     * @ORM\JoinColumn(name="lista_id", referencedColumnName="id")
     */
    protected $lista;

    /**
     * @ORM\ManyToOne(targetEntity="Entidad", inversedBy="campoFormulariosVersion")
     * @ORM\JoinColumn(name="entidad_id", referencedColumnName="id")
     */
    protected $entidad;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $campo_formulario_version_id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $config;

    public function __construct()
    {
        $this->pasoCamposVersion = new ArrayCollection();
        $this->registroCampos = new ArrayCollection();
        $this->registroEntidads = new ArrayCollection();
        $this->registroFechas = new ArrayCollection();
        $this->registroHoras = new ArrayCollection();
        $this->registroListas = new ArrayCollection();
        $this->registroMultiseleccions = new ArrayCollection();
        $this->registroNumericoDecimals = new ArrayCollection();
        $this->registroNumericoEnteros = new ArrayCollection();
        $this->registroNumericoMonedas = new ArrayCollection();
        $this->registroTextoCortos = new ArrayCollection();
        $this->registroTextoLargos = new ArrayCollection();
        $this->registroBooleanos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\CampoFormularioVersion
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
     * Set the value of campo.
     *
     * @param string $campo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setCampo()
    {
        $this->campo = TextUtils::slugifyWithUnderscore("Hola Mundo");

        return $this;
    }

    /**
     * Get the value of campo.
     *
     * @return string
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * Set the value of tipo_campo.
     *
     * @param string $tipo_campo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setTipoCampo($tipo_campo)
    {
        $this->tipo_campo = $tipo_campo;

        return $this;
    }

    /**
     * Get the value of tipo_campo.
     *
     * @return string
     */
    public function getTipoCampo()
    {
        return $this->tipo_campo;
    }

    /**
     * Set the value of valor_cuadro_texto.
     *
     * @param string $valor_cuadro_texto
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setValorCuadroTexto($valor_cuadro_texto)
    {
        $this->valor_cuadro_texto = $valor_cuadro_texto;
        $this->campo = TextUtils::slugifyWithUnderscore($valor_cuadro_texto);


        return $this;
    }

    /**
     * Get the value of valor_cuadro_texto.
     *
     * @return string
     */
    public function getValorCuadroTexto()
    {
        return $this->valor_cuadro_texto;
    }

    /**
     * Set the value of posicion.
     *
     * @param integer $posicion
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setPosicion($posicion)
    {
        $this->posicion = $posicion;

        return $this;
    }

    /**
     * Get the value of posicion.
     *
     * @return integer
     */
    public function getPosicion()
    {
        return $this->posicion;
    }

    /**
     * Set the value of valor_minimo.
     *
     * @param integer $valor_minimo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setValorMinimo($valor_minimo)
    {
        $this->valor_minimo = $valor_minimo;

        return $this;
    }

    /**
     * Get the value of valor_minimo.
     *
     * @return integer
     */
    public function getValorMinimo()
    {
        return $this->valor_minimo;
    }

    /**
     * Set the value of longitud.
     *
     * @param integer $longitud
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setLongitud($longitud)
    {
        if ($longitud == "null") {
            $longitud = null;
        }
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get the value of longitud.
     *
     * @return integer
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set the value of obligatorio.
     *
     * @param boolean $obligatorio
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setObligatorio($obligatorio)
    {
        $this->obligatorio = $obligatorio;

        return $this;
    }

    /**
     * Get the value of obligatorio.
     *
     * @return boolean
     */
    public function getObligatorio()
    {
        return $this->obligatorio;
    }

    /**
     * Set the value of indice.
     *
     * @param boolean $indice
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get the value of indice.
     *
     * @return boolean
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * Set the value of imprime_sticker.
     *
     * @param boolean $imprime_sticker
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setImprimeSticker($imprime_sticker)
    {
        $this->imprime_sticker = $imprime_sticker;

        return $this;
    }

    /**
     * Get the value of imprime_sticker.
     *
     * @return boolean
     */
    public function getImprimeSticker()
    {
        return $this->imprime_sticker;
    }

    /**
     * Set the value of posicion_sticker.
     *
     * @param integer $posicion_sticker
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setPosicionSticker($posicion_sticker)
    {
        $this->posicion_sticker = $posicion_sticker;

        return $this;
    }

    /**
     * Get the value of posicion_sticker.
     *
     * @return integer
     */
    public function getPosicionSticker()
    {
        return $this->posicion_sticker;
    }

    /**
     * Set the value of ayuda.
     *
     * @param string $ayuda
     * @return \App\Entity\CampoFormularioVersion
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
     * Set the value of item_tabla_defecto.
     *
     * @param integer $item_tabla_defecto
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setItemTablaDefecto($item_tabla_defecto)
    {
        $this->item_tabla_defecto = $item_tabla_defecto;

        return $this;
    }

    /**
     * Get the value of item_tabla_defecto.
     *
     * @return integer
     */
    public function getItemTablaDefecto()
    {
        return $this->item_tabla_defecto;
    }

    /**
     * Set the value of valor_defecto.
     *
     * @param string $valor_defecto
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setValorDefecto($valor_defecto)
    {
        $this->valor_defecto = $valor_defecto;

        return $this;
    }

    /**
     * Get the value of valor_defecto.
     *
     * @return string
     */
    public function getValorDefecto()
    {
        return $this->valor_defecto;
    }

    /**
     * Set the value of item_lista_defecto.
     *
     * @param integer $item_lista_defecto
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setItemListaDefecto($item_lista_defecto)
    {
        $this->item_lista_defecto = $item_lista_defecto;

        return $this;
    }

    /**
     * Get the value of item_lista_defecto.
     *
     * @return integer
     */
    public function getItemListaDefecto()
    {
        return $this->item_lista_defecto;
    }

    /**
     * Set the value of mostrar_front.
     *
     * @param boolean $mostrar_front
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setMostrarFront($mostrar_front)
    {
        $this->mostrar_front = $mostrar_front;

        return $this;
    }

    /**
     * Get the value of mostrar_front.
     *
     * @return boolean
     */
    public function getMostrarFront()
    {
        return $this->mostrar_front;
    }

    /**
     * Set the value of posicion_front.
     *
     * @param integer $posicion_front
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setPosicionFront($posicion_front)
    {
        $this->posicion_front = $posicion_front;

        return $this;
    }

    /**
     * Get the value of posicion_front.
     *
     * @return integer
     */
    public function getPosicionFront()
    {
        return $this->posicion_front;
    }

    /**
     * Set the value of formulario_version_id.
     *
     * @param integer $formulario_version_id
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setFormularioVersionId($formulario_version_id)
    {
        $this->formulario_version_id = $formulario_version_id;

        return $this;
    }

    /**
     * Get the value of formulario_version_id.
     *
     * @return integer
     */
    public function getFormularioVersionId()
    {
        return $this->formulario_version_id;
    }

    /**
     * Set the value of lista_id.
     *
     * @param integer $lista_id
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setListaId($lista_id)
    {
        $this->lista_id = $lista_id;

        return $this;
    }

    /**
     * Get the value of lista_id.
     *
     * @return integer
     */
    public function getListaId()
    {
        return $this->lista_id;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setEstadoId($estado_id)
    {
        $this->estado_id = $estado_id;
        $valor_cuadro_texto=explode("|",$this->valor_cuadro_texto);
        if ($estado_id == 0 && isset($valor_cuadro_texto[1]) && $valor_cuadro_texto[1]=="reactivado") {
            $date = new \DateTime();
            $this->valor_cuadro_texto = $valor_cuadro_texto[0] . "|inactivado|" . $date->format("Y_m_d");
        }           
        else if ($estado_id == 1 && isset($valor_cuadro_texto[1])) {            
            $this->valor_cuadro_texto = $valor_cuadro_texto[0] . "|reactivado";
        }        
        else if ($estado_id == 0) {
            $date = new \DateTime();
            $this->valor_cuadro_texto = $this->valor_cuadro_texto . "|inactivado|" . $date->format("Y_m_d");
        }
 
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
     * Set the value of entidad_id.
     *
     * @param integer $entidad_id
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setEntidadId($entidad_id)
    {
        $this->entidad_id = $entidad_id;

        return $this;
    }

    /**
     * Get the value of entidad_id.
     *
     * @return integer
     */
    public function getEntidadId()
    {
        return $this->entidad_id;
    }

    /**
     * Set the value of entidad_column_name.
     *
     * @param string $entidad_column_name
     * @return \App\Entity\CampoFormulario
     */
    public function setEntidadColumnName($entidad_column_name)
    {
        $this->entidad_column_name = $entidad_column_name;
        return $this;
    }

    /**
     * Get the value of entidad_column_name.
     *
     * @return string
     */
    public function getEntidadColumnName()
    {
        return $this->entidad_column_name;
    }

    /**
     * Set the value of campo_formulario_id.
     *
     * @param integer $campo_formulario_id
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setCampoFormularioId($campo_formulario_id)
    {
        $this->campo_formulario_id = $campo_formulario_id;

        return $this;
    }

    /**
     * Get the value of campo_formulario_id.
     *
     * @return integer
     */
    public function getCampoFormularioId()
    {
        return $this->campo_formulario_id;
    }

    /**
     * Set the value of campo_unico.
     *
     * @param boolean $campo_unico
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setCampoUnico($campo_unico)
    {
        $this->campo_unico = $campo_unico;

        return $this;
    }

    /**
     * Get the value of campo_unico.
     *
     * @return boolean
     */
    public function getCampoUnico()
    {
        return $this->campo_unico;
    }

    /**
     * Set the value of oculto_al_radicar.
     *
     * @param boolean $oculto_al_radicar
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setOcultoAlRadicar($oculto_al_radicar)
    {
        $this->oculto_al_radicar = $oculto_al_radicar;

        return $this;
    }

    /**
     * Get the value of oculto_al_radicar.
     *
     * @return boolean
     */
    public function getOcultoAlRadicar()
    {
        return $this->oculto_al_radicar;
    }

    /**
     * Add PasoCampoVersion entity to collection (one to many).
     *
     * @param \App\Entity\PasoCampoVersion $pasoCampoVersion
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addPasoCampoVersion(PasoCampoVersion $pasoCampoVersion)
    {
        $this->pasoCamposVersion[] = $pasoCampoVersion;

        return $this;
    }

    /**
     * Remove PasoCampoVersion entity from collection (one to many).
     *
     * @param \App\Entity\PasoCampoVersion $pasoCampoVersion
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removePasoCampoVersion(PasoCampoVersion $pasoCampoVersion)
    {
        $this->pasoCamposVersion->removeElement($pasoCampoVersion);

        return $this;
    }

    /**
     * Get PasoCampoVersion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPasoCamposVersion()
    {
        return $this->pasoCamposVersion;
    }

    /**
     * Add RegistroCampo entity to collection (one to many).
     *
     * @param \App\Entity\RegistroCampo $registroCampo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroCampo(RegistroCampo $registroCampo)
    {
        $this->registroCampos[] = $registroCampo;

        return $this;
    }

    /**
     * Remove RegistroCampo entity from collection (one to many).
     *
     * @param \App\Entity\RegistroCampo $registroCampo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroCampo(RegistroCampo $registroCampo)
    {
        $this->registroCampos->removeElement($registroCampo);

        return $this;
    }

    /**
     * Get RegistroCampo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroCampos()
    {
        return $this->registroCampos;
    }

    /**
     * Add RegistroEntidad entity to collection (one to many).
     *
     * @param \App\Entity\RegistroEntidad $registroEntidad
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroEntidad(RegistroEntidad $registroEntidad)
    {
        $this->registroEntidads[] = $registroEntidad;

        return $this;
    }

    /**
     * Remove RegistroEntidad entity from collection (one to many).
     *
     * @param \App\Entity\RegistroEntidad $registroEntidad
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroEntidad(RegistroEntidad $registroEntidad)
    {
        $this->registroEntidads->removeElement($registroEntidad);

        return $this;
    }

    /**
     * Get RegistroEntidad entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroEntidads()
    {
        return $this->registroEntidads;
    }

    /**
     * Add RegistroFecha entity to collection (one to many).
     *
     * @param \App\Entity\RegistroFecha $registroFecha
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroFecha(RegistroFecha $registroFecha)
    {
        $this->registroFechas[] = $registroFecha;

        return $this;
    }

    /**
     * Remove RegistroFecha entity from collection (one to many).
     *
     * @param \App\Entity\RegistroFecha $registroFecha
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroFecha(RegistroFecha $registroFecha)
    {
        $this->registroFechas->removeElement($registroFecha);

        return $this;
    }

    /**
     * Get RegistroFecha entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroFechas()
    {
        return $this->registroFechas;
    }

    /**
     * Add RegistroHora entity to collection (one to many).
     *
     * @param \App\Entity\RegistroHora $registroHora
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroHora(RegistroHora $registroHora)
    {
        $this->registroHoras[] = $registroHora;

        return $this;
    }

    /**
     * Remove RegistroHora entity from collection (one to many).
     *
     * @param \App\Entity\RegistroHora $registroHora
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroHora(RegistroHora $registroHora)
    {
        $this->registroHoras->removeElement($registroHora);

        return $this;
    }

    /**
     * Get RegistroHora entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroHoras()
    {
        return $this->registroHoras;
    }

    /**
     * Add RegistroLista entity to collection (one to many).
     *
     * @param \App\Entity\RegistroLista $registroLista
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroLista(RegistroLista $registroLista)
    {
        $this->registroListas[] = $registroLista;

        return $this;
    }

    /**
     * Remove RegistroLista entity from collection (one to many).
     *
     * @param \App\Entity\RegistroLista $registroLista
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroLista(RegistroLista $registroLista)
    {
        $this->registroListas->removeElement($registroLista);

        return $this;
    }

    /**
     * Get RegistroLista entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroListas()
    {
        return $this->registroListas;
    }

    /**
     * Add RegistroMultiseleccion entity to collection (one to many).
     *
     * @param \App\Entity\RegistroMultiseleccion $registroMultiseleccion
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroMultiseleccion(RegistroMultiseleccion $registroMultiseleccion)
    {
        $this->registroMultiseleccions[] = $registroMultiseleccion;

        return $this;
    }

    /**
     * Remove RegistroMultiseleccion entity from collection (one to many).
     *
     * @param \App\Entity\RegistroMultiseleccion $registroMultiseleccion
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroMultiseleccion(RegistroMultiseleccion $registroMultiseleccion)
    {
        $this->registroMultiseleccions->removeElement($registroMultiseleccion);

        return $this;
    }

    /**
     * Get RegistroMultiseleccion entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroMultiseleccions()
    {
        return $this->registroMultiseleccions;
    }

    /**
     * Add RegistroNumericoDecimal entity to collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoDecimal $registroNumericoDecimal
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroNumericoDecimal(RegistroNumericoDecimal $registroNumericoDecimal)
    {
        $this->registroNumericoDecimals[] = $registroNumericoDecimal;

        return $this;
    }

    /**
     * Remove RegistroNumericoDecimal entity from collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoDecimal $registroNumericoDecimal
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroNumericoDecimal(RegistroNumericoDecimal $registroNumericoDecimal)
    {
        $this->registroNumericoDecimals->removeElement($registroNumericoDecimal);

        return $this;
    }

    /**
     * Get RegistroNumericoDecimal entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroNumericoDecimals()
    {
        return $this->registroNumericoDecimals;
    }

    /**
     * Add RegistroNumericoEntero entity to collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoEntero $registroNumericoEntero
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroNumericoEntero(RegistroNumericoEntero $registroNumericoEntero)
    {
        $this->registroNumericoEnteros[] = $registroNumericoEntero;

        return $this;
    }

    /**
     * Remove RegistroNumericoEntero entity from collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoEntero $registroNumericoEntero
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroNumericoEntero(RegistroNumericoEntero $registroNumericoEntero)
    {
        $this->registroNumericoEnteros->removeElement($registroNumericoEntero);

        return $this;
    }

    /**
     * Get RegistroNumericoEntero entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroNumericoEnteros()
    {
        return $this->registroNumericoEnteros;
    }

    /**
     * Add RegistroNumericoMoneda entity to collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoMoneda $registroNumericoMoneda
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroNumericoMoneda(RegistroNumericoMoneda $registroNumericoMoneda)
    {
        $this->registroNumericoMonedas[] = $registroNumericoMoneda;

        return $this;
    }

    /**
     * Remove RegistroNumericoMoneda entity from collection (one to many).
     *
     * @param \App\Entity\RegistroNumericoMoneda $registroNumericoMoneda
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroNumericoMoneda(RegistroNumericoMoneda $registroNumericoMoneda)
    {
        $this->registroNumericoMonedas->removeElement($registroNumericoMoneda);

        return $this;
    }

    /**
     * Get RegistroNumericoMoneda entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroNumericoMonedas()
    {
        return $this->registroNumericoMonedas;
    }

    /**
     * Add RegistroTextoCorto entity to collection (one to many).
     *
     * @param \App\Entity\RegistroTextoCorto $registroTextoCorto
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroTextoCorto(RegistroTextoCorto $registroTextoCorto)
    {
        $this->registroTextoCortos[] = $registroTextoCorto;

        return $this;
    }

    /**
     * Remove RegistroTextoCorto entity from collection (one to many).
     *
     * @param \App\Entity\RegistroTextoCorto $registroTextoCorto
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroTextoCorto(RegistroTextoCorto $registroTextoCorto)
    {
        $this->registroTextoCortos->removeElement($registroTextoCorto);

        return $this;
    }

    /**
     * Get RegistroTextoCorto entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroTextoCortos()
    {
        return $this->registroTextoCortos;
    }

    /**
     * Add RegistroTextoLargo entity to collection (one to many).
     *
     * @param \App\Entity\RegistroTextoLargo $registroTextoLargo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroTextoLargo(RegistroTextoLargo $registroTextoLargo)
    {
        $this->registroTextoLargos[] = $registroTextoLargo;

        return $this;
    }

    /**
     * Remove RegistroTextoLargo entity from collection (one to many).
     *
     * @param \App\Entity\RegistroTextoLargo $registroTextoLargo
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroTextoLargo(RegistroTextoLargo $registroTextoLargo)
    {
        $this->registroTextoLargos->removeElement($registroTextoLargo);

        return $this;
    }

    /**
     * Get RegistroTextoLargo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroTextoLargos()
    {
        return $this->registroTextoLargos;
    }

    /**
     * Set FormularioVersion entity (many to one).
     *
     * @param \App\Entity\FormularioVersion $formularioVersion
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setFormularioVersion(FormularioVersion $formularioVersion = null)
    {
        $this->formularioVersion = $formularioVersion;

        return $this;
    }

    /**
     * Get FormularioVersion entity (many to one).
     *
     * @return \App\Entity\FormularioVersion
     */
    public function getFormularioVersion()
    {
        return $this->formularioVersion;
    }

    /**
     * Set Entidad entity (many to one).
     *
     * @param \App\Entity\Entidad $entidad
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setEntidad(Entidad $entidad = null)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get Entidad entity (many to one).
     *
     * @return \App\Entity\Entidad
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set Lista entity (many to one).
     *
     * @param \App\Entity\Lista $lista
     * @return \App\Entity\CampoFormulario
     */
    public function setLista(Lista $lista = null)
    {
        $this->lista = $lista;

        return $this;
    }

    /**
     * Get Lista entity (many to one).
     *
     * @return \App\Entity\Lista
     */
    public function getLista()
    {
        return $this->lista;
    }


    /**
     * Add RegistroBooleano entity to collection (one to many).
     *
     * @param \App\Entity\RegistroBooleano $registroBooleano
     * @return \App\Entity\CampoFormularioVersion
     */
    public function addRegistroBooelano(RegistroBooleano $registroBooleano)
    {
        $this->registroBooleanos[] = $registroBooleano;

        return $this;
    }

    /**
     * Remove registroBooleano entity from collection (one to many).
     *
     * @param \App\Entity\RegistroBooleano $registroBooleano
     * @return \App\Entity\CampoFormularioVersion
     */
    public function removeRegistroBooleano(RegistroBooleano $registroBooleano)
    {
        $this->registroBooleanos->removeElement($registroBooleano);

        return $this;
    }

    /**
     * Get RegistroBooleano entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistroBooleanos()
    {
        return $this->registroBooleanos;
    }

    /**
     * Set the value of campo_formulario_version_id.
     *
     * @param integer $campo_formulario_version_id
     * @return \App\Entity\CampoFormularioVersion
     */
    public function setCampoFormularioVersionId($campo_formulario_version_id)
    {
        $this->campo_formulario_version_id = $campo_formulario_version_id;

        return $this;
    }

    /**
     * Get the value of campo_formulario_version_id.
     *
     * @return integer
     */
    public function getCampoFormularioVersionId()
    {
        return $this->campo_formulario_version_id;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config): self
    {
        $this->config = $config;

        return $this;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        }
    }

    public function toArray()
    {
        $entidad = null;
        $formularioVersion = null;
        $lista = null;

        if (null !== $this->getEntidad()) {
            $entidad = $this->getEntidad()->getNombre();
        }
        if (null !== $this->getFormularioVersion()) {
            $formularioVersion = $this->getFormularioVersion()->getNombre();
        }
        if (null !== $this->getLista()) {
            $lista = $this->getLista()->getNombre();
        }

        return [
            'id' => $this->getId(),
            'formularioVersion' => $formularioVersion,
            'lista' => $lista,
            'entidad' => $entidad,
            'campo' => $this->getCampo(),
            'tipo_campo' => $this->getTipoCampo(),
            'valor_cuadro_texto' => $this->getValorCuadroTexto(),
            'posicion' => $this->getPosicion(),
            'valor_minimo' => $this->getvalorMinimo(),
            'entidad_column_name' => $this->getEntidadColumnName(),
            'config' => $this->getConfig()
        ];
    }

    public function __sleep()
    {
        return array('id', 'campo', 'tipo_campo', 'valor_cuadro_texto', 'posicion', 'valor_minimo', 'longitud', 'obligatorio', 'indice', 'imprime_sticker', 'posicion_sticker', 'ayuda', 'item_tabla_defecto', 'valor_defecto', 'item_lista_defecto', 'mostrar_front', 'posicion_front', 'formulario_version_id', 'lista_id', 'estado_id', 'entidad_id', 'campo_formulario_id', 'campo_unico', 'oculto_al_radicar', 'entidad_column_name', 'config');
    }
}
