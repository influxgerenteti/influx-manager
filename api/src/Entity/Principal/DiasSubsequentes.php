<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\DiasSubsequentesRepository")
 * @ORM\Table(name="dias_subsequentes")
 */
class DiasSubsequentes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_dia;

    /**
     * @ORM\Column(type="boolean", options={"comment":"Verdadeiro se este dia é para ser o último do mês (calculado conforme o mês)"})
     */
    private $ultimo_dia_mes = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Franqueada", inversedBy="diasSubsequentes")
     */
    private $franqueada;

    public function __construct()
    {
        $this->franqueada = new ArrayCollection();
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

    public function getNumeroDia(): ?int
    {
        return $this->numero_dia;
    }

    public function setNumeroDia(?int $numero_dia): self
    {
        $this->numero_dia = $numero_dia;

        return $this;
    }

    public function getUltimoDiaMes(): ?bool
    {
        return $this->ultimo_dia_mes;
    }

    public function setUltimoDiaMes(bool $ultimo_dia_mes): self
    {
        $this->ultimo_dia_mes = $ultimo_dia_mes;

        return $this;
    }

    /**
     * @return Collection|Franqueada[]
     */
    public function getFranqueada(): Collection
    {
        return $this->franqueada;
    }

    public function addFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueada->contains($franqueada) === false) {
            $this->franqueada[] = $franqueada;
        }

        return $this;
    }

    public function removeFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueada->contains($franqueada) === true) {
            $this->franqueada->removeElement($franqueada);
        }

        return $this;
    }


}
