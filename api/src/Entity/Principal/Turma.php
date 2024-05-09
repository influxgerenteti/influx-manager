<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TurmaRepository")
 * @ORM\Table(name="turma")
 */
class Turma
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
     * @ORM\Column(type="string", length=1, nullable=false, options={"default"="R", "comment"="(R)egular, (S)emi-intensivo, (I)ntensivo"})
     */
    private $intensidade = 'R';

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $maximo_alunos;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $data_inicio;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_fim;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ModalidadeTurma")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modalidade_turma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario")
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada")
     */
    private $sala_franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Horario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $horario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ValorHoraLinhas")
     */
    private $valor_hora_linhas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="string", length=3, nullable=false, options={"default"="ABE", "comment"="(ABE)berta, (ENC)errada, em (FOR)mação"})
     */
    private $situacao = 'FOR';

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0})
     */
    private $excluido = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="turmas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="turma")
     */
    private $contratos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Semestre", inversedBy="turmasSemestre")
     * @ORM\JoinColumn(nullable=false)
     */
    private $semestre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Curso", inversedBy="turmas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $curso;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TurmaAula", mappedBy="turma")
     */
    private $turmaAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="turma")
     */
    private $reposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="turma")
     */
    private $alunoAvaliacaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="turma")
     */
    private $alunoAvaliacaoConceituals;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;


    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    public function __construct()
    {
        $this->contratos            = new ArrayCollection();
        $this->turmaAulas           = new ArrayCollection();
        $this->alunoAtividadeExtras = new ArrayCollection();
        $this->reposicaoAulas       = new ArrayCollection();
        $this->alunoAvaliacaos      = new ArrayCollection();
        $this->alunoAvaliacaoConceituals = new ArrayCollection();
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

    public function getIntensidade(): ?string
    {
        return $this->intensidade;
    }

    public function setIntensidade(string $intensidade): self
    {
        $this->intensidade = $intensidade;

        return $this;
    }

    public function getMaximoAlunos(): ?string
    {
        return $this->maximo_alunos;
    }

    public function setMaximoAlunos(string $maximo_alunos): self
    {
        $this->maximo_alunos = $maximo_alunos;

        return $this;
    }

    public function getDataInicio() : ? \DateTimeInterface
    {
        return $this->data_inicio;
    }

    public function setDataInicio(\DateTimeInterface $data_inicio) : self
    {
        $this->data_inicio = $data_inicio;

        return $this;
    }

    public function getDataFim() : ? \DateTimeInterface
    {
        return $this->data_fim;
    }

    public function setDataFim(\DateTimeInterface $data_fim) : self
    {
        $this->data_fim = $data_fim;

        return $this;
    }

    public function getModalidadeTurma() : ? ModalidadeTurma
    {
        return $this->modalidade_turma;
    }

    public function setModalidadeTurma(? ModalidadeTurma $modalidade_turma) : self
    {
        $this->modalidade_turma = $modalidade_turma;

        return $this;
    }

    public function getLivro() : ? Livro
    {
        return $this->livro;
    }

    public function setLivro(? Livro $livro) : self
    {
        $this->livro = $livro;

        return $this;
    }

    public function getFuncionario() : ? Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(? Funcionario $funcionario) : self
    {
        $this->funcionario = $funcionario;

        return $this;
    }

    public function getSalaFranqueada() : ? SalaFranqueada
    {
        return $this->sala_franqueada;
    }

    public function setSalaFranqueada(? SalaFranqueada $sala_franqueada) : self
    {
        $this->sala_franqueada = $sala_franqueada;

        return $this;
    }

    public function getHorario() : ? Horario
    {
        return $this->horario;
    }

    public function setHorario(? Horario $horario) : self
    {
        $this->horario = $horario;

        return $this;
    }

    public function getValorHoraLinhas() : ? ValorHoraLinhas
    {
        return $this->valor_hora_linhas;
    }

    public function setValorHoraLinhas(? ValorHoraLinhas $valor_hora_linhas) : self
    {
        $this->valor_hora_linhas = $valor_hora_linhas;

        return $this;
    }

    public function getObservacao() : ? string
    {
        return $this->observacao;
    }

    public function setObservacao(string $observacao) : self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getSituacao() : ? string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao) : self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getExcluido() : ? string
    {
        return $this->excluido;
    }

    public function setExcluido(string $excluido) : self
    {
        $this->excluido = $excluido;

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

    /**
     * @return Collection|Contrato[]
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    public function addContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === false) {
            $this->contratos[] = $contrato;
            $contrato->setTurma($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getTurma() === $this) {
                $contrato->setTurma(null);
            }
        }

        return $this;
    }

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): self
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * @return Collection|TurmaAula[]
     */
    public function getTurmaAulas(): Collection
    {
        return $this->turmaAulas;
    }

    public function addTurmaAula(TurmaAula $turmaAula): self
    {
        if ($this->turmaAulas->contains($turmaAula) === false) {
            $this->turmaAulas[] = $turmaAula;
            $turmaAula->setTurma($this);
        }

        return $this;
    }

    public function removeTurmaAula(TurmaAula $turmaAula): self
    {
        if ($this->turmaAulas->contains($turmaAula) === true) {
            $this->turmaAulas->removeElement($turmaAula);
            // set the owning side to null (unless already changed)
            if ($turmaAula->getTurma() === $this) {
                $turmaAula->setTurma(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulas(): Collection
    {
        return $this->reposicaoAulas;
    }

    public function addReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === false) {
            $this->reposicaoAulas[] = $reposicaoAula;
            $reposicaoAula->setTurma($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getTurma() === $this) {
                $reposicaoAula->setTurma(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|AlunoAvaliacao[]
     */
    public function getAlunoAvaliacaos(): Collection
    {
        return $this->alunoAvaliacaos;
    }

    public function addAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === false) {
            $this->alunoAvaliacaos[] = $alunoAvaliacao;
            $alunoAvaliacao->setTurma($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === true) {
            $this->alunoAvaliacaos->removeElement($alunoAvaliacao);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacao->getTurma() === $this) {
                $alunoAvaliacao->setTurma(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceituals(): Collection
    {
        return $this->alunoAvaliacaoConceituals;
    }

    public function addAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === false) {
            $this->alunoAvaliacaoConceituals[] = $alunoAvaliacaoConceitual;
            $alunoAvaliacaoConceitual->setTurma($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === true) {
            $this->alunoAvaliacaoConceituals->removeElement($alunoAvaliacaoConceitual);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitual->getTurma() === $this) {
                $alunoAvaliacaoConceitual->setTurma(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSponteId(): ?string
    {
        return $this->sponte_id;
    }

    /**
     * @param string|null $sponte_id
     */
    public function setSponteId(?string $sponte_id): void
    {
        $this->sponte_id = $sponte_id;
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
