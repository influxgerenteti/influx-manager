<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MetaFranqueadaRepository")
 */
class MetaFranqueada
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="metaFranqueadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="integer")
     */
    private $ano;

    /**
     * @ORM\Column(type="integer")
     */
    private $mes;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MetaFranqueadaHistorico", mappedBy="meta_franqueada", orphanRemoval=true)
     */
    private $metaFranqueadaHistoricos;

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

    public function __construct()
    {
        $this->metaFranqueadaHistoricos = new ArrayCollection();
    }

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

    public function getAno(): ?int
    {
        return $this->ano;
    }

    public function setAno(int $ano): self
    {
        $this->ano = $ano;

        return $this;
    }

    public function getMes(): ?int
    {
        return $this->mes;
    }

    public function setMes(int $mes): self
    {
        $this->mes = $mes;

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

    /**
     * @return Collection|MetaFranqueadaHistorico[]
     */
    public function getMetaFranqueadaHistoricos(): Collection
    {
        return $this->metaFranqueadaHistoricos;
    }

    public function addMetaFranqueadaHistorico(MetaFranqueadaHistorico $metaFranqueadaHistorico): self
    {
        if ($this->metaFranqueadaHistoricos->contains($metaFranqueadaHistorico) === false) {
            $this->metaFranqueadaHistoricos[] = $metaFranqueadaHistorico;
            $metaFranqueadaHistorico->setMetaFranqueada($this);
        }

        return $this;
    }

    public function removeMetaFranqueadaHistorico(MetaFranqueadaHistorico $metaFranqueadaHistorico): self
    {
        if ($this->metaFranqueadaHistoricos->contains($metaFranqueadaHistorico) === true) {
            $this->metaFranqueadaHistoricos->removeElement($metaFranqueadaHistorico);
            // set the owning side to null (unless already changed)
            if ($metaFranqueadaHistorico->getMetaFranqueada() === $this) {
                $metaFranqueadaHistorico->setMetaFranqueada(null);
            }
        }

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
