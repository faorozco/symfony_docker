<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\EventosRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\Eventos\ListEvents;
use App\Controller\Eventos\ListEventsPasos;

/**
 * @ApiResource(
 *    attributes={"pagination_items_per_page"=100},
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
*     itemOperations={
 *      "listEvents"={
 *         "method"="GET",
 *          "path"="/eventos/list_events",
 *          "controller"=ListEvents::class,
 *          "defaults"={"_api_receive"=false}
 *        },
 *     "listEventsPasos"={
 *         "method"="POST",
 *          "path"="/eventos/list_events_paso",
 *          "controller"=ListEventsPasos::class,
 *          "defaults"={"_api_receive"=false}
 *        } 
 *  }  
 * )
 * *
 * @ORM\Entity()
 * @ORM\Table(name="eventos")
 */
class Eventos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $father;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $icon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $component;

    /**
     * @ORM\OneToMany(targetEntity="PasoEvento", mappedBy="evento")
     * @ORM\JoinColumn(name="id", referencedColumnName="evento_id", nullable=false)
     */
    protected $pasoEvento;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 1})
     */
    protected $estado_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fatherId;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFather(): ?string
    {
        return $this->father;
    }

    public function setFather(string $father): self
    {
        $this->father = $father;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getComponent(): ?string
    {
        return $this->component;
    }

    public function setComponent(string $component): self
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Set the value of estado_id.
     *
     * @param integer $estado_id
     * @return \App\Entity\Eventos
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

    public function getFatherId(): ?int
    {
        return $this->fatherId;
    }

    public function setFatherId(?int $fatherId): self
    {
        $this->fatherId = $fatherId;

        return $this;
    }
}
