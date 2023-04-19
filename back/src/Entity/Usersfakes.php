<?php

namespace App\Entity;

use App\Repository\UsersfakesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsersfakesRepository::class)
 */
class Usersfakes
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
    private $login;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $try;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $bloqueo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fake;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getTry(): ?int
    {
        return $this->try;
    }

    public function setTry(?int $try): self
    {
        $this->try = $try;

        return $this;
    }

    public function getBloqueo(): ?bool
    {
        return $this->bloqueo;
    }

    public function setBloqueo(?bool $bloqueo): self
    {
        $this->bloqueo = $bloqueo;

        return $this;
    }

    public function getFake(): ?string
    {
        return $this->fake;
    }

    public function setFake(?string $fake): self
    {
        $this->fake = $fake;

        return $this;
    }
}
