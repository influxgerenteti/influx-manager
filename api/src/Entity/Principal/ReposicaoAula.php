<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ReposicaoAulaRepository")
 * @ORM\Table(name="reposicao_aula")
 */
class ReposicaoAula
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Turma", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $turma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Licao", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $licao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada", inversedBy="reposicaoAulas")
     */
    private $sala_franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario_solicitante;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="responsavelReposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsavel_execucao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descricao_atividade;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_hora_inicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_hora_fim;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $presenca;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\OcorrenciaAcademica", inversedBy="reposicaoAula", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $ocorrencia_academica;

    /**
     * @ORM\Column(type="string", length=2, options={"default":"P", "comment":"P - Pendente, C - Concluido, CC - Cancelado"})
     */
    private $situacao = 'P';

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="reposicao_aula", cascade={"persist", "remove"})
     */
    private $historicoReposicaoAula;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento", inversedBy="reposicaoAulas")
     */
    private $forma_cobranca;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_mid_term_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_mid_term_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_mid_term_composition = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true, options={"comment":"Somatoria do Final Test + Composition"})
     */
    private $nota_final_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_final_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_final_composition = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true, options={"comment":"Somatoria do Test + Composition"})
     */
    private $nota_retake_mid_term_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_mid_term_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_mid_term_composition = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true, options={"comment":"Somatoria do Test + Composition"})
     */
    private $nota_retake_final_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_final_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_final_composition = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="reposicaoAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AlunoAvaliacao", inversedBy="reposicaoAulas")
     */
    private $aluno_avaliacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaReceber", inversedBy="reposicaoAulas")
     */
    private $conta_receber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AlunoDiario", inversedBy="reposicaoAulas")
     */
    private $aluno_diario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="reposicaoAulasNmto")
     */
    private $nota_mid_term_oral;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="reposicaoAulasNfo")
     */
    private $nota_final_oral;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="reposicaoAulasNrmto")
     */
    private $nota_retake_mid_term_oral;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="reposicaoAulasNrfo")
     */
    private $nota_retake_final_oral;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoReposicaoAula", mappedBy="reposicao_aula", orphanRemoval=true)
     */
    private $pagamentoReposicaoAulas;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isenta;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $valor;

    public function __construct()
    {
        $this->pagamentoReposicaoAulas = new ArrayCollection();
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

    public function getLivro(): ?Livro
    {
        return $this->livro;
    }

    public function setLivro(?Livro $livro): self
    {
        $this->livro = $livro;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

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

    public function getSalaFranqueada(): ?SalaFranqueada
    {
        return $this->sala_franqueada;
    }

    public function setSalaFranqueada(?SalaFranqueada $sala_franqueada): self
    {
        $this->sala_franqueada = $sala_franqueada;

        return $this;
    }

    public function getUsuarioSolicitante(): ?Usuario
    {
        return $this->usuario_solicitante;
    }

    public function setUsuarioSolicitante(?Usuario $usuario_solicitante): self
    {
        $this->usuario_solicitante = $usuario_solicitante;

        return $this;
    }

    public function getResponsavelExecucao(): ?Funcionario
    {
        return $this->responsavel_execucao;
    }

    public function setResponsavelExecucao(?Funcionario $responsavel_execucao): self
    {
        $this->responsavel_execucao = $responsavel_execucao;

        return $this;
    }

    public function getDescricaoAtividade(): ?string
    {
        return $this->descricao_atividade;
    }

    public function setDescricaoAtividade(string $descricao_atividade): self
    {
        $this->descricao_atividade = $descricao_atividade;

        return $this;
    }

    public function getDataHoraInicio(): ?\DateTimeInterface
    {
        return $this->data_hora_inicio;
    }

    public function setDataHoraInicio(\DateTimeInterface $data_hora_inicio): self
    {
        $this->data_hora_inicio = $data_hora_inicio;

        return $this;
    }

    public function getDataHoraFim(): ?\DateTimeInterface
    {
        return $this->data_hora_fim;
    }

    public function setDataHoraFim(\DateTimeInterface $data_hora_fim): self
    {
        $this->data_hora_fim = $data_hora_fim;

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

    public function getOcorrenciaAcademica(): ?OcorrenciaAcademica
    {
        return $this->ocorrencia_academica;
    }

    public function setOcorrenciaAcademica(OcorrenciaAcademica $ocorrencia_academica): self
    {
        $this->ocorrencia_academica = $ocorrencia_academica;

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

    public function getHistoricoReposicaoAula(): ?HistoricoReposicaoAula
    {
        return $this->historicoReposicaoAula;
    }

    public function setHistoricoReposicaoAula(HistoricoReposicaoAula $historicoReposicaoAula): self
    {
        $this->historicoReposicaoAula = $historicoReposicaoAula;

        // set the owning side of the relation if necessary
        if ($this !== $historicoReposicaoAula->getReposicaoAula()) {
            $historicoReposicaoAula->setReposicaoAula($this);
        }

        return $this;
    }

    public function getFormaCobranca(): ?FormaPagamento
    {
        return $this->forma_cobranca;
    }

    public function setFormaCobranca(?FormaPagamento $forma_cobranca): self
    {
        $this->forma_cobranca = $forma_cobranca;

        return $this;
    }

    public function getNotaMidTermEscrita()
    {
        return $this->nota_mid_term_escrita;
    }

    public function setNotaMidTermEscrita($nota_mid_term_escrita): self
    {
        $this->nota_mid_term_escrita = $nota_mid_term_escrita;

        return $this;
    }

    public function getNotaMidTermTest()
    {
        return $this->nota_mid_term_test;
    }

    public function setNotaMidTermTest($nota_mid_term_test): self
    {
        $this->nota_mid_term_test = $nota_mid_term_test;

        return $this;
    }

    public function getNotaMidTermComposition()
    {
        return $this->nota_mid_term_composition;
    }

    public function setNotaMidTermComposition($nota_mid_term_composition): self
    {
        $this->nota_mid_term_composition = $nota_mid_term_composition;

        return $this;
    }

    public function getNotaFinalEscrita()
    {
        return $this->nota_final_escrita;
    }

    public function setNotaFinalEscrita($nota_final_escrita): self
    {
        $this->nota_final_escrita = $nota_final_escrita;

        return $this;
    }

    public function getNotaFinalTest()
    {
        return $this->nota_final_test;
    }

    public function setNotaFinalTest($nota_final_test): self
    {
        $this->nota_final_test = $nota_final_test;

        return $this;
    }

    public function getNotaFinalComposition()
    {
        return $this->nota_final_composition;
    }

    public function setNotaFinalComposition($nota_final_composition): self
    {
        $this->nota_final_composition = $nota_final_composition;

        return $this;
    }

    public function getNotaRetakeMidTermEscrita()
    {
        return $this->nota_retake_mid_term_escrita;
    }

    public function setNotaRetakeMidTermEscrita($nota_retake_mid_term_escrita): self
    {
        $this->nota_retake_mid_term_escrita = $nota_retake_mid_term_escrita;

        return $this;
    }

    public function getNotaRetakeMidTermTest()
    {
        return $this->nota_retake_mid_term_test;
    }

    public function setNotaRetakeMidTermTest($nota_retake_mid_term_test): self
    {
        $this->nota_retake_mid_term_test = $nota_retake_mid_term_test;

        return $this;
    }

    public function getNotaRetakeMidTermComposition()
    {
        return $this->nota_retake_mid_term_composition;
    }

    public function setNotaRetakeMidTermComposition($nota_retake_mid_term_composition): self
    {
        $this->nota_retake_mid_term_composition = $nota_retake_mid_term_composition;

        return $this;
    }

    public function getNotaRetakeFinalEscrita()
    {
        return $this->nota_retake_final_escrita;
    }

    public function setNotaRetakeFinalEscrita($nota_retake_final_escrita): self
    {
        $this->nota_retake_final_escrita = $nota_retake_final_escrita;

        return $this;
    }

    public function getNotaRetakeFinalTest()
    {
        return $this->nota_retake_final_test;
    }

    public function setNotaRetakeFinalTest($nota_retake_final_test): self
    {
        $this->nota_retake_final_test = $nota_retake_final_test;

        return $this;
    }

    public function getNotaRetakeFinalComposition()
    {
        return $this->nota_retake_final_composition;
    }

    public function setNotaRetakeFinalComposition($nota_retake_final_composition): self
    {
        $this->nota_retake_final_composition = $nota_retake_final_composition;

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

    public function getAlunoAvaliacao(): ?AlunoAvaliacao
    {
        return $this->aluno_avaliacao;
    }

    public function setAlunoAvaliacao(?AlunoAvaliacao $aluno_avaliacao): self
    {
        $this->aluno_avaliacao = $aluno_avaliacao;

        return $this;
    }

    public function getContaReceber(): ?ContaReceber
    {
        return $this->conta_receber;
    }

    public function setContaReceber(?ContaReceber $conta_receber): self
    {
        $this->conta_receber = $conta_receber;

        return $this;
    }

    public function getAlunoDiario(): ?AlunoDiario
    {
        return $this->aluno_diario;
    }

    public function setAlunoDiario(?AlunoDiario $aluno_diario): self
    {
        $this->aluno_diario = $aluno_diario;

        return $this;
    }

    public function getNotaMidTermOral(): ?ConceitoAvaliacao
    {
        return $this->nota_mid_term_oral;
    }

    public function setNotaMidTermOral(?ConceitoAvaliacao $nota_mid_term_oral): self
    {
        $this->nota_mid_term_oral = $nota_mid_term_oral;

        return $this;
    }

    public function getNotaFinalOral(): ?ConceitoAvaliacao
    {
        return $this->nota_final_oral;
    }

    public function setNotaFinalOral(?ConceitoAvaliacao $nota_final_oral): self
    {
        $this->nota_final_oral = $nota_final_oral;

        return $this;
    }

    public function getNotaRetakeMidTermOral(): ?ConceitoAvaliacao
    {
        return $this->nota_retake_mid_term_oral;
    }

    public function setNotaRetakeMidTermOral(?ConceitoAvaliacao $nota_retake_mid_term_oral): self
    {
        $this->nota_retake_mid_term_oral = $nota_retake_mid_term_oral;

        return $this;
    }

    public function getNotaRetakeFinalOral(): ?ConceitoAvaliacao
    {
        return $this->nota_retake_final_oral;
    }

    public function setNotaRetakeFinalOral(?ConceitoAvaliacao $nota_retake_final_oral): self
    {
        $this->nota_retake_final_oral = $nota_retake_final_oral;

        return $this;
    }

    /**
     * @return Collection|PagamentoReposicaoAula[]
     */
    public function getPagamentoReposicaoAulas(): Collection
    {
        return $this->pagamentoReposicaoAulas;
    }

    public function addPagamentoReposicaoAula(PagamentoReposicaoAula $pagamentoReposicaoAula): self
    {
        if ($this->pagamentoReposicaoAulas->contains($pagamentoReposicaoAula) === false) {
            $this->pagamentoReposicaoAulas[] = $pagamentoReposicaoAula;
            $pagamentoReposicaoAula->setReposicaoAula($this);
        }

        return $this;
    }

    public function removePagamentoReposicaoAula(PagamentoReposicaoAula $pagamentoReposicaoAula): self
    {
        if ($this->pagamentoReposicaoAulas->contains($pagamentoReposicaoAula) === true) {
            $this->pagamentoReposicaoAulas->removeElement($pagamentoReposicaoAula);
            // set the owning side to null (unless already changed)
            if ($pagamentoReposicaoAula->getReposicaoAula() === $this) {
                $pagamentoReposicaoAula->setReposicaoAula(null);
            }
        }

        return $this;
    }

    public function getIsenta(): ?bool
    {
        return $this->isenta;
    }

    public function setIsenta(?bool $isenta): self
    {
        $this->isenta = $isenta;

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }


}
