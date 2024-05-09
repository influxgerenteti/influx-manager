<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MetaFranqueadaHistoricoRepository")
 */
class MetaFranqueadaHistorico
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="metaFranqueadaHistoricos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\MetaFranqueada", inversedBy="metaFranqueadaHistoricos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meta_franqueada;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meta_1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meta_2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meta_3;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meta_franqueadora_1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meta_franqueadora_2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meta_franqueadora_3;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getMetaFranqueada(): ?MetaFranqueada
    {
        return $this->meta_franqueada;
    }

    public function setMetaFranqueada(?MetaFranqueada $meta_franqueada): self
    {
        $this->meta_franqueada = $meta_franqueada;

        return $this;
    }

    public function getMeta1(): ?int
    {
        return $this->meta_1;
    }

    public function setMeta1(?int $meta_1): self
    {
        $this->meta_1 = $meta_1;

        return $this;
    }

    public function getMeta2(): ?int
    {
        return $this->meta_2;
    }

    public function setMeta2(?int $meta_2): self
    {
        $this->meta_2 = $meta_2;

        return $this;
    }

    public function getMeta3(): ?int
    {
        return $this->meta_3;
    }

    public function setMeta3(?int $meta_3): self
    {
        $this->meta_3 = $meta_3;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getMetaFranqueadora1(): ?int
    {
        return $this->meta_franqueadora_1;
    }

    public function setMetaFranqueadora1(?int $meta_franqueadora_1): self
    {
        $this->meta_franqueadora_1 = $meta_franqueadora_1;

        return $this;
    }

    public function getMetaFranqueadora2(): ?int
    {
        return $this->meta_franqueadora_2;
    }

    public function setMetaFranqueadora2(?int $meta_franqueadora_2): self
    {
        $this->meta_franqueadora_2 = $meta_franqueadora_2;

        return $this;
    }

    public function getMetaFranqueadora3(): ?int
    {
        return $this->meta_franqueadora_3;
    }

    public function setMetaFranqueadora3(?int $meta_franqueadora_3): self
    {
        $this->meta_franqueadora_3 = $meta_franqueadora_3;

        return $this;
    }


}
