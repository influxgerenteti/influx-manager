<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\SemestreRepository")
 */
class Semestre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $descricao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_inicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_termino;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="semestre")
     */
    private $contratosSemestre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Turma", mappedBy="semestre")
     */
    private $turmasSemestre;

    public function __construct()
    {
        $this->contratosSemestre = new ArrayCollection();
        $this->turmasSemestre    = new ArrayCollection();
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

    public function getDataInicio(): ?\DateTimeInterface
    {
        return $this->data_inicio;
    }

    public function setDataInicio(\DateTimeInterface $data_inicio): self
    {
        $this->data_inicio = $data_inicio;

        return $this;
    }

    public function getDataTermino(): ?\DateTimeInterface
    {
        return $this->data_termino;
    }

    public function setDataTermino(\DateTimeInterface $data_termino): self
    {
        $this->data_termino = $data_termino;

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratosSemestre(): Collection
    {
        return $this->contratosSemestre;
    }

    public function addContratosSemestre(Contrato $contratosSemestre): self
    {
        if ($this->contratosSemestre->contains($contratosSemestre) === false) {
            $this->contratosSemestre[] = $contratosSemestre;
            $contratosSemestre->setSemestre($this);
        }

        return $this;
    }

    public function removeContratosSemestre(Contrato $contratosSemestre): self
    {
        if ($this->contratosSemestre->contains($contratosSemestre) === true) {
            $this->contratosSemestre->removeElement($contratosSemestre);
            // set the owning side to null (unless already changed)
            if ($contratosSemestre->getSemestre() === $this) {
                $contratosSemestre->setSemestre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Turma[]
     */
    public function getTurmasSemestre(): Collection
    {
        return $this->turmasSemestre;
    }

    public function addTurmasSemestre(Turma $turmasSemestre): self
    {
        if ($this->turmasSemestre->contains($turmasSemestre) === false) {
            $this->turmasSemestre[] = $turmasSemestre;
            $turmasSemestre->setSemestre($this);
        }

        return $this;
    }

    public function removeTurmasSemestre(Turma $turmasSemestre): self
    {
        if ($this->turmasSemestre->contains($turmasSemestre) === true) {
            $this->turmasSemestre->removeElement($turmasSemestre);
            // set the owning side to null (unless already changed)
            if ($turmasSemestre->getSemestre() === $this) {
                $turmasSemestre->setSemestre(null);
            }
        }

        return $this;
    }


}
