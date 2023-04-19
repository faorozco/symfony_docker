<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints\Json;
use App\Controller\Comments\SaveComments;
use App\Controller\Comments\GetComments;
/**
 * @ApiResource(
 *   collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST"}
 *    },
 *   itemOperations={
 *      "postMessages"={
 *         "method"="POST",
 *         "path"="/comment_paso/save",
 *          "controller"=SaveComments::class,
 *          "validate"=false,
 *           "defaults"={"_api_receive"=false}
 *        },
 *       "postGetMessages"={
 *         "method"="POST",
 *         "path"="/comment_paso/get",
 *          "controller"=GetComments::class,
 *          "validate"=false,
 *          "defaults"={"_api_receive"=false}
 *        },
 *      "get"={
 *         "method"="GET",
 *         "path"="/comment_pasos/{id}",
 *          "requirements"={"id"="\d+"}
 *        }
 *  }
 * 
 * )
 * App\Entity\CommentPaso
 * @ORM\Entity()
 * @ORM\Table(name="CommentPaso")
 */

class CommentPaso
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
    private $ejecucion_paso_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre_completo;

    /**
     * @ORM\Column(type="string", length=1200, nullable=true)
     */
    private $comentario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type_comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEjecucionPasoId(): ?int
    {
        return $this->ejecucion_paso_id;
    }

    public function setEjecucionPasoId(int $ejecucion_paso_id): self
    {
        $this->ejecucion_paso_id = $ejecucion_paso_id;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNombreCompleto(): ?string
    {
        return $this->nombre_completo;
    }

    public function setNombreCompleto(string $nombre_completo): self
    {
        $this->nombre_completo = $nombre_completo;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;

        return $this;
    }

    public function getTypeComment(): ?string
    {
        return $this->type_comment;
    }

    public function setTypeComment(?string $type_comment): self
    {
        $this->type_comment = $type_comment;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
}
