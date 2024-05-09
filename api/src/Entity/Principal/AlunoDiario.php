<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunoDiarioRepository")
 * @ORM\Table(name="aluno_diario")
 */
class AlunoDiario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="alunoDiarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoDiarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Curso", inversedBy="alunoDiarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $curso;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TurmaAula", inversedBy="alunoDiarios")
     */
    private $turma_aula;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="alunoDiarios")
     * @ORM\JoinColumn(nullable=true)
     */
    private $funcionario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="string", length=2, options={"default":"P", "comment":"(P)resente, (F)altante, (R)eposição"})
     */
    private $presenca = 'P';

    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"default":"E", "comment":"(E)ntregue, (EA)ntregue com Atraso, (NE)ão Entregue"})
     */
    private $atividade_ca = null;

    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"default":"E", "comment":"(E)ntregue, (EA)ntregue com Atraso, (NE)ão Entregue"})
     */
    private $atividade_ce = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro", inversedBy="alunoDiarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada", inversedBy="alunoDiarios")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sala_franqueada;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Licao", inversedBy="alunoDiarios")
     */
    private $licao;

    /**
     *
     * @ORM\Column(type="boolean",options={"default":"0"})
     */
    private $reposicao_aula = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="aluno_diario")
     */
    private $reposicaoAulas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="alunoDiarios")
     */
    private $contrato;

    public function __construct()
    {
        $this->data_criacao   = new \DateTime();
        $this->licao          = new ArrayCollection();
        $this->reposicaoAulas = new ArrayCollection();
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

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

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

    public function getTurmaAula(): ?TurmaAula
    {
        return $this->turma_aula;
    }

    public function setTurmaAula(?TurmaAula $turma_aula): self
    {
        $this->turma_aula = $turma_aula;

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

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getPresenca(): ?string
    {
        return $this->presenca;
    }

    public function setPresenca(string $presenca): self
    {
        $this->presenca = $presenca;

        return $this;
    }

    public function getAtividadeCa(): ?string
    {
        return $this->atividade_ca;
    }

    public function setAtividadeCa(?string $atividade_ca): self
    {
        $this->atividade_ca = $atividade_ca;

        return $this;
    }

    public function getAtividadeCe(): ?string
    {
        return $this->atividade_ce;
    }

    public function setAtividadeCe(?string $atividade_ce): self
    {
        $this->atividade_ce = $atividade_ce;

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

    public function getLivro(): ?Livro
    {
        return $this->livro;
    }

    public function setLivro(?Livro $livro): self
    {
        $this->livro = $livro;

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

    /**
     * @return Collection|Licao[]
     */
    public function getLicao(): Collection
    {
        return $this->licao;
    }

    public function addLicao(Licao $licao): self
    {
        if ($this->licao->contains($licao) === false) {
            $this->licao[] = $licao;
        }

        return $this;
    }

    public function removeLicao(Licao $licao): self
    {
        if ($this->licao->contains($licao) === true) {
            $this->licao->removeElement($licao);
        }

        return $this;
    }

    public function getReposicaoAula(): ?bool
    {
        return $this->reposicao_aula;
    }

    public function setReposicaoAula(bool $reposicao_aula): self
    {
        $this->reposicao_aula = $reposicao_aula;

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
            $reposicaoAula->setAlunoDiario($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getAlunoDiario() === $this) {
                $reposicaoAula->setAlunoDiario(null);
            }
        }

        return $this;
    }

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

        return $this;
    }


}
