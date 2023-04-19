<?php
//TODO: Validar los campos al lado del cliente, si viene NULL que no los envie.(OCurre con  usar front  e imprime sticker)


namespace App\Entity;

use App\Utils\TextUtils;
use App\Filter\ORSearchFilter;
use App\Controller\EntityLister;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\FormFieldLister;
use App\Controller\CampoFormularioUpdate;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Controller\CampoFormulario\CampoFormularioCreate;
use App\Controller\CamposFormularioUpdate;
use Doctrine\ORM\Mapping\UniqueConstraint;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\CampoFormulario\CampoFormularioSaveIndex;

/**
 * @ApiResource(
 *    attributes={"pagination_items_per_page"=100},
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "post"={
 *          "method"="POST",
 *          "path"="/campo_formularios/create",
 *          "controller"=CampoFormularioCreate::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "put"={
 *          "method"="PUT",
 *          "path"="/campo_formularios/{id}",
 *          "controller"=CampoFormularioUpdate::class,
 *          "defaults"={"_api_receive"=false}
 *       },
 *      "actualizarRegistro"={
 *          "method"="PUT",
 *          "path"="/update_campo_formularios",
 *          "controller"=CamposFormularioUpdate::class,
 *          "defaults"={"_api_receive"=false}
 *          },
 *      "get"={
 *         "method"="GET",
 *         "path"="/campo_formularios/{id}",
 *          "requirements"={"id"="\d+"}
 *        },
 *       "getEspecial"={
 *         "method"="GET",
 *         "path"="/campo_formularios/{id}/listFields",
 *         "controller"=EntityLister::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *        },
 *       "getFormFieldsValues"={
 *         "method"="GET",
 *         "path"="/campo_formularios/{id}/getformfieldsvalues",
 *         "controller"=FormFieldLister::class,
 *         "defaults"={
 *              "_items_per_page"=10
 *          }
 *        },
 *        "saveIndex"={
 *          "method"="POST",
 *          "path"="/campo_formularios/saveIndex",
 *          "controller"=CampoFormularioSaveIndex::class,
 *          "defaults"={"_api_receive"=false}
 *       },
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
 *     fields={"formulario", "valor_cuadro_texto", "estado_id"},
 *     errorPath="valor_cuadro_texto",
 *     message="Esta etiqueta ya esta siendo usada en este formulario."
 * )
 * App\Entity\CampoFormulario
 *
 * @ORM\Entity(repositoryClass="App\Repository\CampoFormularioRepository")
 * @ORM\Table(name="campo_formulario",
 *          indexes={
 *              @ORM\Index(
 *                  name="fk_campo_formulario_formulario1_idx",
 *                  columns={"formulario_id"}
 *              ),
 *              @ORM\Index(
 *                  name="fk_campo_formulario_Lista1_idx",
 *                  columns={"lista_id"}
 *              ),
 *              @ORM\Index(
 *                  name="fk_campo_formulario_entidad1_idx",
 *                  columns={"entidad_id"}
 *              )
 *          }
 * )
 */
class CampoFormulario
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
    protected $formulario_id;

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
     * @ORM\OneToMany(targetEntity="PasoCampo", mappedBy="campoFormulario")
     * @ORM\JoinColumn(name="id", referencedColumnName="campo_formulario_id", nullable=false)
     */
    protected $pasoCampos;

    /**
     * @ORM\ManyToOne(targetEntity="Formulario", inversedBy="campoFormularios")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=false)
     * @ApiSubresource(maxDepth=1)
     */
    protected $formulario;

    /**
     * @ORM\ManyToOne(targetEntity="Lista", inversedBy="campoFormularios")
     * @ORM\JoinColumn(name="lista_id", referencedColumnName="id")
     */
    protected $lista;

    /**
     * @ORM\ManyToOne(targetEntity="Entidad", inversedBy="campoFormularios")
     * @ORM\JoinColumn(name="entidad_id", referencedColumnName="id")
     */
    protected $entidad;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $config;

    public function __construct()
    {
        $this->pasoCampos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\CampoFormulario
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

    /**
     * Set the value of lista_id.
     *
     * @param integer $lista_id
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * @return \App\Entity\CampoFormulario
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
     * Add PasoCampo entity to collection (one to many).
     *
     * @param \App\Entity\PasoCampo $pasoCampo
     * @return \App\Entity\CampoFormulario
     */
    public function addPasoCampo(PasoCampo $pasoCampo)
    {
        $this->pasoCampos[] = $pasoCampo;

        return $this;
    }

    /**
     * Remove PasoCampo entity from collection (one to many).
     *
     * @param \App\Entity\PasoCampo $pasoCampo
     * @return \App\Entity\CampoFormulario
     */
    public function removePasoCampo(PasoCampo $pasoCampo)
    {
        $this->pasoCampos->removeElement($pasoCampo);

        return $this;
    }

    /**
     * Get PasoCampo entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPasoCampos()
    {
        return $this->pasoCampos;
    }

    /**
     * Set Formulario entity (many to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\CampoFormulario
     */
    public function setFormulario(Formulario $formulario = null)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Get Formulario entity (many to one).
     *
     * @return \App\Entity\Formulario
     */
    public function getFormulario()
    {
        return $this->formulario;
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
     * Set Entidad entity (many to one).
     *
     * @param \App\Entity\Entidad $entidad
     * @return \App\Entity\CampoFormulario
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
        $formulario = null;
        $lista = null;

        if (null !== $this->getEntidad()) {
            $entidad = $this->getEntidad()->getNombre();
        }
        if (null !== $this->getFormulario()) {
            $formulario = $this->getFormulario()->getNombre();
        }
        if (null !== $this->getLista()) {
            $lista = $this->getLista()->getNombre();
        }

        return [
            'id' => $this->getId(),
            'formulario' => $formulario,
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
        return array('id', 'campo', 'tipo_campo', 'valor_cuadro_texto', 'posicion', 'valor_minimo', 'longitud', 'obligatorio', 'indice', 'imprime_sticker', 'posicion_sticker', 'ayuda', 'item_tabla_defecto', 'valor_defecto', 'item_lista_defecto', 'mostrar_front', 'posicion_front', 'formulario_id', 'lista_id', 'estado_id', 'entidad_id', 'campo_formulario_id', 'campo_unico', 'oculto_al_radicar', 'entidad_column_name', 'config');
    }
}
