<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\HorarioRepository")
 */
class Horario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = "A";

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HorarioAula", mappedBy="horario")
     */
    private $horarioAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Turma", mappedBy="horario")
     */
    private $turmas;

    public function __construct()
    {
        $this->horarioAulas = new ArrayCollection();
        $this->turmas       = new ArrayCollection();
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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    /**
     * @return Collection|HorarioAula[]
     */
    public function getHorarioAulas(): Collection
    {
        return $this->horarioAulas;
    }

    public function addHorarioAula(HorarioAula $horarioAula): self
    {
        if ($this->horarioAulas->contains($horarioAula) === false) {
            $this->horarioAulas[] = $horarioAula;
            $horarioAula->setHorario($this);
        }

        return $this;
    }

    public function removeHorarioAula(HorarioAula $horarioAula): self
    {
        if ($this->horarioAulas->contains($horarioAula) === true) {
            $this->horarioAulas->removeElement($horarioAula);
            // set the owning side to null (unless already changed)
            if ($horarioAula->getHorario() === $this) {
                $horarioAula->setHorario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Turma[]
     */
    public function getTurmas(): Collection
    {
        return $this->turmas;
    }


}
