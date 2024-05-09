<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TurmaAulaRepository")
 * @ORM\Table(name="turma_aula")
 */
class TurmaAula
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="turmaAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Turma", inversedBy="turmaAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $turma;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_aula;

    /**
     * @ORM\Column(type="boolean", options={"comment":"Finalizada ou não"})
     */
    private $finalizada = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="turma_aula")
     */
    private $alunoDiarios;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Licao", inversedBy="turmaAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $licao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="turmaAulasDadas")
     */
    private $funcionario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoTurmaAula", mappedBy="turma_aula", orphanRemoval=true)
     */
    private $pagamentoTurmaAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademicaDetalhes", mappedBy="ocorrencia_academica")
     * @ORM\OrderBy({"data_criacao"="DESC"})
     */
    private $ocorrenciaAcademicaDetalhes;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    public function __construct()
    {
        $this->alunoDiarios        = new ArrayCollection();
        $this->pagamentoTurmaAulas = new ArrayCollection();
        $this->ocorrenciaAcademicaDetalhes = new ArrayCollection();
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

    public function getTurma(): ?Turma
    {
        return $this->turma;
    }

    public function setTurma(?Turma $turma): self
    {
        $this->turma = $turma;

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

    public function getFinalizada(): ?bool
    {
        return $this->finalizada;
    }

    public function setFinalizada(bool $finalizada): self
    {
        $this->finalizada = $finalizada;

        return $this;
    }

    /**
     * @return Collection|AlunoDiario[]
     */
    public function getAlunoDiarios(): Collection
    {
        return $this->alunoDiarios;
    }

    public function addAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === false) {
            $this->alunoDiarios[] = $alunoDiario;
            $alunoDiario->setTurmaAula($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getTurmaAula() === $this) {
                $alunoDiario->setTurmaAula(null);
            }
        }

        return $this;
    }

    public function getLicao(): ?Licao
    {
        return $this->licao;
    }

    public function setLicao(?Licao $licao): self
    {
        $this->licao = $licao;

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
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

    /**
     * @return Collection|PagamentoTurmaAula[]
     */
    public function getPagamentoTurmaAulas(): Collection
    {
        return $this->pagamentoTurmaAulas;
    }

    public function addPagamentoTurmaAula(PagamentoTurmaAula $pagamentoTurmaAula): self
    {
        if ($this->pagamentoTurmaAulas->contains($pagamentoTurmaAula) === false) {
            $this->pagamentoTurmaAulas[] = $pagamentoTurmaAula;
            $pagamentoTurmaAula->setTurmaAula($this);
        }

        return $this;
    }

    public function removePagamentoTurmaAula(PagamentoTurmaAula $pagamentoTurmaAula): self
    {
        if ($this->pagamentoTurmaAulas->contains($pagamentoTurmaAula) === true) {
            $this->pagamentoTurmaAulas->removeElement($pagamentoTurmaAula);
            // set the owning side to null (unless already changed)
            if ($pagamentoTurmaAula->getTurmaAula() === $this) {
                $pagamentoTurmaAula->setTurmaAula(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|OcorrenciaAcademicaDetalhes[]
     */
    public function getOcorrenciaAcademicaDetalhes(): Collection
    {
        return $this->ocorrenciaAcademicaDetalhes;
    }

    public function addOcorrenciaAcademicaDetalhe(OcorrenciaAcademicaDetalhes $ocorrenciaAcademicaDetalhe): self
    {
        if ($this->ocorrenciaAcademicaDetalhes->contains($ocorrenciaAcademicaDetalhe) === false) {
            $this->ocorrenciaAcademicaDetalhes[] = $ocorrenciaAcademicaDetalhe;
            $ocorrenciaAcademicaDetalhe->setTurmaAula($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademicaDetalhe(OcorrenciaAcademicaDetalhes $ocorrenciaAcademicaDetalhe): self
    {
        if ($this->ocorrenciaAcademicaDetalhes->contains($ocorrenciaAcademicaDetalhe) === true) {
            $this->ocorrenciaAcademicaDetalhes->removeElement($ocorrenciaAcademicaDetalhe);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademicaDetalhe->getTurmaAula() === $this) {
                $ocorrenciaAcademicaDetalhe->setTurmaAula(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt(\DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }


}
