<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\BonusClassRepository")
 */
class BonusClass
{


    public function __construct()
    {
        $this->data_criacao          = new \DateTime();
        $this->alunosBonusClasses    = new ArrayCollection();
        $this->pagamentoBonusClasses = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="bonusClasses")
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada", inversedBy="bonusClasses")
     */
    private $sala_franqueada;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_aula;

    /**
     * @ORM\Column(type="time")
     */
    private $horario_inicio;

    /**
     * @ORM\Column(type="time")
     */
    private $horario_termino;

    /**
     *
     * @ORM\Column(type="string", length=3, options={"comment":"(PEN)DENTE, (CON)CLUÍDO", "default":"PEN"})
     */
    private $situacao = 'PEN';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunosBonusClass", mappedBy="bonus_class")
     */
    private $alunosBonusClasses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoBonusClass", mappedBy="bonus_class", orphanRemoval=true)
     */
    private $pagamentoBonusClasses;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="bonusClasses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

        return $this;
    }

    public function getSalaFranqueada(): ?SalaFranqueada
    {
        return $this->sala_franqueada;
    }

    public function setSalaFranqueada(?SalaFranqueada $sala_franqueada): self
    {
        $this->sala_franqueada = $sala_franqueada;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(?\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getDataAula(): ?\DateTimeInterface
    {
        return $this->data_aula;
    }

    public function setDataAula(\DateTimeInterface $data_aula): self
    {
        $this->data_aula = $data_aula;

        return $this;
    }

    public function getHorarioInicio(): ?\DateTimeInterface
    {
        return $this->horario_inicio;
    }

    public function setHorarioInicio(\DateTimeInterface $horario_inicio): self
    {
        $this->horario_inicio = $horario_inicio;

        return $this;
    }

    public function getHorarioTermino(): ?\DateTimeInterface
    {
        return $this->horario_termino;
    }

    public function setHorarioTermino(\DateTimeInterface $horario_termino): self
    {
        $this->horario_termino = $horario_termino;

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
     * @return Collection|AlunosBonusClass[]
     */
    public function getAlunosBonusClasses(): Collection
    {
        return $this->alunosBonusClasses;
    }

    public function addAlunosBonusClass(AlunosBonusClass $alunosBonusClass): self
    {
        if ($this->alunosBonusClasses->contains($alunosBonusClass) === false) {
            $this->alunosBonusClasses[] = $alunosBonusClass;
            $alunosBonusClass->setBonusClass($this);
        }

        return $this;
    }

    public function removeAlunosBonusClass(AlunosBonusClass $alunosBonusClass): self
    {
        if ($this->alunosBonusClasses->contains($alunosBonusClass) === true) {
            $this->alunosBonusClasses->removeElement($alunosBonusClass);
            // set the owning side to null (unless already changed)
            if ($alunosBonusClass->getBonusClass() === $this) {
                $alunosBonusClass->setBonusClass(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoBonusClass[]
     */
    public function getPagamentoBonusClasses(): Collection
    {
        return $this->pagamentoBonusClasses;
    }

    public function addPagamentoBonusClass(PagamentoBonusClass $pagamentoBonusClass): self
    {
        if ($this->pagamentoBonusClasses->contains($pagamentoBonusClass) === false) {
            $this->pagamentoBonusClasses[] = $pagamentoBonusClass;
            $pagamentoBonusClass->setBonusClass($this);
        }

        return $this;
    }

    public function removePagamentoBonusClass(PagamentoBonusClass $pagamentoBonusClass): self
    {
        if ($this->pagamentoBonusClasses->contains($pagamentoBonusClass) === true) {
            $this->pagamentoBonusClasses->removeElement($pagamentoBonusClass);
            // set the owning side to null (unless already changed)
            if ($pagamentoBonusClass->getBonusClass() === $this) {
                $pagamentoBonusClass->setBonusClass(null);
            }
        }

        return $this;
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


}
