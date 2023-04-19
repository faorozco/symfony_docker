<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\ExecuteCustomMasterQuery;
use App\Controller\ExportCustomMasterQuery;
use App\Controller\ExecuteMasterQuery;
use App\Controller\ExportMasterQuery;
use App\Controller\ConsultaMaestraSave;
use App\Filter\ORSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={
 *          "method"="POST",
 *          "path"="/consulta_maestras",
 *          "controller"=ConsultaMaestraSave::class,
 *          "defaults"={"_api_receive"=false}
 *      },
 *      "executecustomquery"={
 *          "method"="POST",
 *          "path"="/consulta_maestras/execute/customquery",
 *          "controller"=ExecuteCustomMasterQuery::class,
 *              "defaults"={
 *                      "_items_per_page"=10
 *              }
 *          },
 *      "exportcustomquery"={
 *          "method"="POST",
 *          "path"="/consulta_maestras/export/customquery",
 *          "controller"=ExportCustomMasterQuery::class,
 *              "defaults"={
 *                      "_items_per_page"=10
 *              }
 *       },
 *       "execute"={
 *          "method"="GET",
 *          "path"="/consulta_maestras/{id}/execute",
 *          "controller"=ExecuteMasterQuery::class,
 *              "defaults"={
 *                      "_items_per_page"=10
 *              }
 *       },
 *      "export"={
 *          "method"="GET",
 *          "path"="/consulta_maestras/{id}/export",
 *          "controller"=ExportMasterQuery::class,
 *              "defaults"={
 *                      "_items_per_page"=10
 *              }
 *          }
 *    },
 *   itemOperations={
 *      "put",
 *      "get"={
 *         "method"="GET",
 *         "path"="/consulta_maestras/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * )
 * @ApiFilter(
 *      ORSearchFilter::class,
 *      properties={"nombre","detalle"}
 * )
 *
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"nombre","detalle"},
 *      arguments={"orderParameterName"="order"})
 *
 * App\Entity\ConsultaMaestra
 *
 * @ORM\Entity()
 * @ORM\Table(name="consulta_maestra", indexes={@ORM\Index(name="fk_consulta_maestra_formulario_idx", columns={"formulario_id"})})
 */
class ConsultaMaestra
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
    protected $nombre;

    /**
     * @ORM\Column(type="text")
     */
    protected $detalle;

    /**
     * @ORM\Column(type="integer")
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $formulario_id;

    /**
     * @ORM\ManyToOne(targetEntity="Formulario", inversedBy="consultaMaestras")
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id", nullable=false)
     */
    protected $formulario;

    /**
     * @ORM\ManyToMany(targetEntity="Grupo", inversedBy="consultaMaestras")
     * @ORM\JoinTable(name="consulta_maestra_grupo",
     *     joinColumns={@ORM\JoinColumn(name="consulta_maestra_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="grupo_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $grupos;


    public function __construct()
    {
        $this->grupos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\ConsultaMaestra
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
     * @return \App\Entity\ConsultaMaestra
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
     * Set the value of detalle.
     *
     * @param string $detalle
     * @return \App\Entity\ConsultaMaestra
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get the value of detalle.
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\ConsultaMaestra
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
     * Set the value of formulario_id.
     *
     * @param integer $formulario_id
     * @return \App\Entity\ConsultaMaestra
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
     * Set Formulario entity (many to one).
     *
     * @param \App\Entity\Formulario $formulario
     * @return \App\Entity\ConsultaMaestra
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
     * Add Grupo entity to collection.
     *
     * @param \App\Entity\Grupo $grupo
     * @return \App\Entity\ConsultaMaestra
     */
    public function addGrupo(Grupo $grupo)
    {
        $this->grupos[] = $grupo;
        $grupo->addConsultaMaestra($this);

        return $this;
    }

    /**
     * Remove Grupo entity from collection.
     *
     * @param \App\Entity\Grupo $grupo
     * @return \App\Entity\ConsultaMaestra
     */
    public function removeGrupo(Grupo $grupo)
    {
        $grupo->removeConsultaMaestra($this);
        $this->grupos->removeElement($grupo);

        return $this;
    }

    /**
     * Get Grupo entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    public function __sleep()
    {
        return array('id', 'nombre', 'detalle', 'estado_id', 'formulario_id');
    }
}
