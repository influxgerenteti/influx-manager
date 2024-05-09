<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\IdiomaRepository")
 */
class Idioma
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $sigla;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="idiomas")
     */
    private $interessados;

    public function __construct()
    {
        $this->interessados = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    public function setSigla(string $sigla): self
    {
        $this->sigla = $sigla;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return Collection|Interessado[]
     */
    public function getInteressados(): Collection
    {
        return $this->interessados;
    }

    public function addInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === false) {
            $this->interessados[] = $interessado;
            $interessado->addIdioma($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            $interessado->removeIdioma($this);
        }

        return $this;
    }


}
