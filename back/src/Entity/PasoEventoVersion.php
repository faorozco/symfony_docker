<?php

namespace App\Entity;

use App\Repository\PasoEventoVersionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity()
* @ORM\Table(name="paso_evento_version")
*/
class PasoEventoVersion
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
    protected $paso_evento_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $paso_version_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $evento_id;

    /**
     * @ORM\Column(type="json")
     */
    private $config = [];

    /**
     * @ORM\ManyToOne(targetEntity="PasoVersion", inversedBy="eventos")
     * @ORM\JoinColumn(name="paso_version_id", referencedColumnName="id", nullable=false)
     */
    protected $pasoVersion;

    /**
     * @ORM\ManyToOne(targetEntity="Eventos", inversedBy="pasoEventoVersion")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id", nullable=true)
     */
    protected $evento;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fatherId;

    public function __construct()
    {}

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\PasoEventoVersion
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
     * Set the value of paso_evento_id.
     *
     * @param integer $paso_evento_id
     * @return \App\Entity\PasoEvento
     */
    public function setPasoEventoId(int $paso_evento_id): self
    {
        $this->paso_evento_id = $paso_evento_id;

        return $this;
    }

    /**
     * Get the value of paso_evento_id.
     *
     * @return integer
     */
    public function getPasoEventoId()
    {
        return $this->paso_evento_id;
    }

    /**
     * Set the value of paso_version_id.
     *
     * @param integer $paso_version_id
     * @return \App\Entity\PasoEventoVersion
     */
    public function setPasoVersionId(int $paso_version_id): self
    {
        $this->paso_version_id = $paso_version_id;

        return $this;
    }

    /**
     * Get the value of paso_version_id.
     *
     * @return integer
     */
    public function getPasoVersionId()
    {
        return $this->paso_version_id;
    }

    /**
     * Set the value of evento_id.
     *
     * @param integer $evento_id
     * @return \App\Entity\PasoEventoVersion
     */
    public function setEventoId(int $evento_id): self
    {
        $this->evento_id = $evento_id;

        return $this;
    }

    /**
     * Get the value of evento_id.
     *
     * @return integer
     */
    public function getEventoId()
    {
        return $this->evento_id;
    }

    /**
     * Set the value of config.
     *
     * @param self $config
     * @return \App\Entity\PasoEventoVersion
     */
    public function setConfig($config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the value of evento_id.
     *
     * @return self
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set Paso entity (many to one).
     *
     * @param \App\Entity\PasoVersion $pasoVersion
     * @return \App\Entity\PasoEventoVersion
     */
    public function setPasoVersion(PasoVersion $pasoVersion)
    {
        $this->pasoVersion = $pasoVersion;

        return $this;
    }

    /**
     * Get the value of evento_id.
     *
     * @return \App\Entity\PasoVersion
     */
    public function getPasoVersion()
    {
        return $this->pasoVersion;
    }

    /**
     * Set Eventos entity.
     *
     * @param \App\Entity\Eventos $eventos
     * @return \App\Entity\PasoEventoVersion
     */
    public function setEvento(Eventos $evento)
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get the value of evento.
     *
     * @return \App\Entity\Evento
     */
    public function getEvento()
    {
        return $this->evento;
    }

    public function __sleep()
    {
        return array('id', 'paso_evento_id', 'paso_version_id', 'evento_id', 'config');
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
