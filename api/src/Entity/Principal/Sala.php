<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\SalaRepository")
 */
class Sala
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\SalaFranqueada", mappedBy="sala")
     */
    private $salaFranqueadas;

    public function __construct()
    {
        $this->salaFranqueadas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|SalaFranqueada[]
     */
    public function getSalaFranqueadas(): Collection
    {
        return $this->salaFranqueadas;
    }

    public function addSalaFranqueada(SalaFranqueada $salaFranqueada): self
    {
        if ($this->salaFranqueadas->contains($salaFranqueada) === false) {
            $this->salaFranqueadas[] = $salaFranqueada;
            $salaFranqueada->setSala($this);
        }

        return $this;
    }

    public function removeSalaFranqueada(SalaFranqueada $salaFranqueada): self
    {
        if ($this->salaFranqueadas->contains($salaFranqueada) === true) {
            $this->salaFranqueadas->removeElement($salaFranqueada);
            // set the owning side to null (unless already changed)
            if ($salaFranqueada->getSala() === $this) {
                $salaFranqueada->setSala(null);
            }
        }

        return $this;
    }


}
