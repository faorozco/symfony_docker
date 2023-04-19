<?php

namespace App\Entity;

use App\Repository\PasoEventoRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Json;
use App\Controller\PasoEvento\ConfigEvents;
use App\Controller\PasoEvento\CreateEvents;
use App\Controller\PasoEvento\DeleteEvents;
/**
 * @ApiResource(
 *    collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
*     itemOperations={
 *      "searchConfig"={
 *         "method"="POST",
 *          "path"="/eventos/config_event",
 *          "controller"=ConfigEvents::class,
 *          "defaults"={"_api_receive"=false}
 *        },
 *      "createConfigEvent"={
 *          "method"="POST",
 *          "path"="/eventos/create_event",
 *          "controller"=CreateEvents::class,
 *          "defaults"={"_api_receive"=false}
 *        },
 *      "DeleteConfigEvent"={
 *          "method"="POST",
 *          "path"="/eventos/delete_event",
 *          "controller"=DeleteEvents::class,
 *          "defaults"={"_api_receive"=false}
 *        },
 *      "get"={
 *         "method"="GET",
 *         "path"="/eventos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }  
 * )
 * *
 * @ORM\Entity()
 * @ORM\Table(name="paso_evento")
 */
class PasoEvento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $paso_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $evento_id;

    /**
     * @ORM\Column(type="json")
     */
    private $config;

    /**
     * @ORM\ManyToOne(targetEntity="Paso", inversedBy="eventos")
     * @ORM\JoinColumn(name="paso_id", referencedColumnName="id", nullable=false)
     */
    protected $paso;

    /**
     * @ORM\ManyToOne(targetEntity="Eventos", inversedBy="pasoEvento")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id", nullable=true)
     */
    protected $evento;

    /**
     * @ORM\Column(type="integer")
     */
    private $fatherId;


    /**
     * Set Eventos entity (one to one).
     *
     * @param \App\Entity\Eventos $eventos
     * @return \App\Entity\FlujoTrabajo
     */
    public function setEvento(Eventos $evento)
    {
        $this->evento = $evento;

        return $this;
    }

    public function getEvento()
    {
        return $this->evento;
    }

    public function getPaso(): ?int
    {
        return $this->paso;
    }


    /**
     * Set Paso entity (many to one).
     *
     * @param \App\Entity\Paso $paso
     * @return \App\Entity\PasoEvento
     */
    public function setPaso(Paso $paso = null)
    {
        $this->paso = $paso;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPasoId(): ?int
    {
        return $this->paso_id;
    }

    public function setPasoId(int $paso_id): self
    {
        $this->paso_id = $paso_id;

        return $this;
    }

    public function getEventoId(): ?int
    {
        return $this->evento_id;
    }

    public function setEventoId(int $evento_id): self
    {
        $this->evento_id = $evento_id;

        return $this;
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

    public function getFatherId(): ?int
    {
        return $this->fatherId;
    }

    public function setFatherId(int $fatherId): self
    {
        $this->fatherId = $fatherId;

        return $this;
    }
}

