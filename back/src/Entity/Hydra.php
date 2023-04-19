<?php

namespace App\Entity;

use App\Repository\HydraRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ActiveLicense;

/**
 * @ApiResource(
 * itemOperations={
 *      "search"={
 *          "method"="POST",
 *          "path"="licencia/search",
 *          "controller"=ActiveLicense::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 * }
 * )
 * @ORM\Entity(repositoryClass=HydraRepository::class)
 */


class Hydra
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
    private $l_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $captcha;

    /**
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @ORM\Column(type="integer")
     */
    private $actual;

    /**
     * @ORM\Column(type="integer")
     */
    private $mf2a;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastDateConfirm;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $statusLocal;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastDateConfirmServer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLId(): ?string
    {
        return $this->l_id;
    }

    public function setLId(string $l_id): self
    {
        $this->l_id = $l_id;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCaptcha(): ?int
    {
        return $this->captcha;
    }

    public function setCaptcha(int $captcha): self
    {
        $this->captcha = $captcha;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getActual(): ?int
    {
        return $this->actual;
    }

    public function setActual(int $actual): self
    {
        $this->actual = $actual;

        return $this;
    }

    public function getMf2a(): ?int
    {
        return $this->mf2a;
    }

    public function setMf2a(int $mf2a): self
    {
        $this->mf2a = $mf2a;

        return $this;
    }

    public function getLastDateConfirm(): ?\DateTimeInterface
    {
        return $this->lastDateConfirm;
    }

    public function setLastDateConfirm(?\DateTimeInterface $lastDateConfirm): self
    {
        $this->lastDateConfirm = $lastDateConfirm;

        return $this;
    }

    public function getStatusLocal(): ?bool
    {
        return $this->statusLocal;
    }

    public function setStatusLocal(?bool $statusLocal): self
    {
        $this->statusLocal = $statusLocal;

        return $this;
    }

    public function getLastDateConfirmServer(): ?\DateTimeInterface
    {
        return $this->lastDateConfirmServer;
    }

    public function setLastDateConfirmServer(?\DateTimeInterface $lastDateConfirmServer): self
    {
        $this->lastDateConfirmServer = $lastDateConfirmServer;

        return $this;
    }
}
