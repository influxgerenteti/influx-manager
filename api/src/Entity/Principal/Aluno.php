<?php

namespace App\Entity\Principal;

use App\Entity\Principal\AlunosBonusClass;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunoRepository")
 */
class Aluno
{
    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Pessoa", cascade={"persist", "remove"}, inversedBy="alunos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pessoa;

    /**
     *
     * @ORM\Column(type="boolean",options={"default":"0"})
     */
    private $excluido = false;

    /**
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $cod_aluno_importado;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ClassificacaoAluno")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classificacao_aluno;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     *
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=false, options={"default"="INT","fixed"=true,"comment"="ATI - Ativo, INA - Inativo, INT - Interessado, TRA - Trancado"})
     */
    private $situacao = 'INT';

    /**
     *
     * @var \App\Entity\Principal\Escolaridade
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Escolaridade")
     * @ORM\JoinColumn(nullable=true)
     */

    private $escolaridade;

    /**
     *
     * @var \App\Entity\Principal\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa")
     * @ORM\JoinColumn(nullable=true)
     */
    private $responsavel_financeiro_pessoa;

    /**
     *
     * @var \App\Entity\Principal\RelacionamentoAluno
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\RelacionamentoAluno")
     * @ORM\JoinColumn(nullable=true)
     */
    private $responsavel_financeiro_relacionamento_aluno;

    /**
     *
     * @var \App\Entity\Principal\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa")
     * @ORM\JoinColumn(nullable=true)
     */
    private $responsavel_didatico_pessoa;

    /**
     *
     * @var \App\Entity\Principal\RelacionamentoAluno
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\RelacionamentoAluno")
     * @ORM\JoinColumn(nullable=true)
     */
    private $responsavel_didatico_relacionamento_aluno;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="aluno")
     */
    private $contratos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaReceber", mappedBy="aluno")
     */
    private $alunoContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="aluno")
     */
    private $alunoTituloRecebers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $emancipado = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="aluno")
     */
    private $interessados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="aluno")
     */
    private $alunoDiarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="aluno")
     */
    private $alunoAvaliacaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="aluno")
     */
    private $alunoAvaliacaoConceituals;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAtividadeExtra", mappedBy="aluno")
     */
    private $alunoAtividadeExtras;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoSituacaoAluno", mappedBy="aluno")
     */
    private $historicoSituacaoAlunos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Servico", mappedBy="aluno")
     */
    private $servicos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="aluno")
     */
    private $reposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\DescontoSuperAmigo", mappedBy="aluno")
     */
    private $descontoSuperAmigos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiarioPersonal", mappedBy="aluno", orphanRemoval=true)
     */
    private $alunoDiarioPersonals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunosBonusClass", mappedBy="aluno")
     */
    private $alunosBonusClasses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\EmprestimoBiblioteca", mappedBy="aluno")
     */
    private $emprestimoBibliotecas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoDollar", mappedBy="contrato")
     */
    private $movimentoDollars;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademica", mappedBy="usuario")
     */
    private $ocorrenciaAcademicas;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Midia", inversedBy="alunos")
     */
    private $tipo_visibilidade;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;


    public function __construct()
    {
        $this->contratos           = new ArrayCollection();
        $this->alunoContaReceber   = new ArrayCollection();
        $this->alunoTituloRecebers = new ArrayCollection();
        $this->interessados        = new ArrayCollection();
        $this->alunoDiarios        = new ArrayCollection();
        $this->alunoAvaliacaos     = new ArrayCollection();
        $this->alunoAvaliacaoConceituals = new ArrayCollection();
        $this->alunoAtividadeExtras      = new ArrayCollection();
        $this->historicoSituacaoAlunos   = new ArrayCollection();
        $this->servicos            = new ArrayCollection();
        $this->reposicaoAulas      = new ArrayCollection();
        $this->descontoSuperAmigos = new ArrayCollection();
        $this->alunosBonusClasses  = new ArrayCollection();
        $this->alunoDiarioPersonals  = new ArrayCollection();
        $this->emprestimoBibliotecas = new ArrayCollection();
        $this->movimentoDollars      = new ArrayCollection();
        $this->ocorrenciaAcademicas  = new ArrayCollection();
        $this->tipo_visibilidade     = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(Pessoa $pessoa): self
    {
        $this->pessoa = $pessoa;

        return $this;
    }

    public function getExcluido(): ?bool
    {
        return $this->excluido;
    }

    public function setExcluido(bool $excluido): self
    {
        $this->excluido = $excluido;

        return $this;
    }

    public function getCodAlunoImportado(): ?string
    {
        return $this->cod_aluno_importado;
    }

    public function setCodAlunoImportado(?string $cod_aluno_importado): self
    {
        $this->cod_aluno_importado = $cod_aluno_importado;

        return $this;
    }

    public function getClassificacaoAluno(): ?ClassificacaoAluno
    {
        return $this->classificacao_aluno;
    }

    public function setClassificacaoAluno(?ClassificacaoAluno $classificacao_aluno): self
    {
        $this->classificacao_aluno = $classificacao_aluno;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

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

    public function getEscolaridade(): ?Escolaridade
    {
        return $this->escolaridade;
    }

    public function setEscolaridade(?Escolaridade $escolaridade): self
    {
        $this->escolaridade = $escolaridade;

        return $this;
    }

    public function getResponsavelFinanceiroPessoa(): ?Pessoa
    {
        return $this->responsavel_financeiro_pessoa;
    }

    public function setResponsavelFinanceiroPessoa(?Pessoa $responsavel_financeiro_pessoa): self
    {
        $this->responsavel_financeiro_pessoa = $responsavel_financeiro_pessoa;

        return $this;
    }

    public function getResponsavelFinanceiroRelacionamentoAluno(): ?RelacionamentoAluno
    {
        return $this->responsavel_financeiro_relacionamento_aluno;
    }

    public function setResponsavelFinanceiroRelacionamentoAluno(?RelacionamentoAluno $responsavel_financeiro_relacionamento_aluno): self
    {
        $this->responsavel_financeiro_relacionamento_aluno = $responsavel_financeiro_relacionamento_aluno;

        return $this;
    }

    public function getResponsavelDidaticoPessoa(): ?Pessoa
    {
        return $this->responsavel_didatico_pessoa;
    }

    public function setResponsavelDidaticoPessoa(?Pessoa $responsavel_didatico_pessoa): self
    {
        $this->responsavel_didatico_pessoa = $responsavel_didatico_pessoa;

        return $this;
    }

    public function getResponsavelDidaticoRelacionamentoAluno(): ?RelacionamentoAluno
    {
        return $this->responsavel_didatico_relacionamento_aluno;
    }

    public function setResponsavelDidaticoRelacionamentoAluno(?RelacionamentoAluno $responsavel_didatico_relacionamento_aluno): self
    {
        $this->responsavel_didatico_relacionamento_aluno = $responsavel_didatico_relacionamento_aluno;

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
            $contrato->setAluno($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getAluno() === $this) {
                $contrato->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContaReceber[]
     */
    public function getAlunoContaReceber(): Collection
    {
        return $this->alunoContaReceber;
    }

    public function addAlunoContaReceber(ContaReceber $alunoContaReceber): self
    {
        if ($this->alunoContaReceber->contains($alunoContaReceber) === false) {
            $this->alunoContaReceber[] = $alunoContaReceber;
            $alunoContaReceber->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoContaReceber(ContaReceber $alunoContaReceber): self
    {
        if ($this->alunoContaReceber->contains($alunoContaReceber) === true) {
            $this->alunoContaReceber->removeElement($alunoContaReceber);
            // set the owning side to null (unless already changed)
            if ($alunoContaReceber->getAluno() === $this) {
                $alunoContaReceber->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getAlunoTituloRecebers(): Collection
    {
        return $this->alunoTituloRecebers;
    }

    public function addAlunoTituloReceber(TituloReceber $alunoTituloReceber): self
    {
        if ($this->alunoTituloRecebers->contains($alunoTituloReceber) === false) {
            $this->alunoTituloRecebers[] = $alunoTituloReceber;
            $alunoTituloReceber->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoTituloReceber(TituloReceber $alunoTituloReceber): self
    {
        if ($this->alunoTituloRecebers->contains($alunoTituloReceber) === true) {
            $this->alunoTituloRecebers->removeElement($alunoTituloReceber);
            // set the owning side to null (unless already changed)
            if ($alunoTituloReceber->getAluno() === $this) {
                $alunoTituloReceber->setAluno(null);
            }
        }

        return $this;
    }

    public function getEmancipado(): ?bool
    {
        return $this->emancipado;
    }

    public function setEmancipado(bool $emancipado): self
    {
        $this->emancipado = $emancipado;
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
            $interessado->setAluno($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            // set the owning side to null (unless already changed)
            if ($interessado->getAluno() === $this) {
                $interessado->setAluno(null);
            }
        }

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
            $alunoDiario->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getAluno() === $this) {
                $alunoDiario->setAluno(null);
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
            $alunoAvaliacao->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === true) {
            $this->alunoAvaliacaos->removeElement($alunoAvaliacao);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacao->getAluno() === $this) {
                $alunoAvaliacao->setAluno(null);
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
            $alunoAvaliacaoConceitual->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === true) {
            $this->alunoAvaliacaoConceituals->removeElement($alunoAvaliacaoConceitual);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitual->getAluno() === $this) {
                $alunoAvaliacaoConceitual->setAluno(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|AlunoAtividadeExtra[]
     */
    public function getAlunoAtividadeExtras(): Collection
    {
        return $this->alunoAtividadeExtras;
    }

    public function addAlunoAtividadeExtra(AlunoAtividadeExtra $alunoAtividadeExtra): self
    {
        if ($this->alunoAtividadeExtras->contains($alunoAtividadeExtra) === false) {
            $this->alunoAtividadeExtras[] = $alunoAtividadeExtra;
            $alunoAtividadeExtra->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoAtividadeExtra(AlunoAtividadeExtra $alunoAtividadeExtra): self
    {
        if ($this->alunoAtividadeExtras->contains($alunoAtividadeExtra) === true) {
            $this->alunoAtividadeExtras->removeElement($alunoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($alunoAtividadeExtra->getAluno() === $this) {
                $alunoAtividadeExtra->setAluno(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|HistoricoSituacaoAluno[]
     */
    public function getHistoricoSituacaoAlunos(): Collection
    {
        return $this->historicoSituacaoAlunos;
    }

    public function addHistoricoSituacaoAluno(HistoricoSituacaoAluno $historicoSituacaoAluno): self
    {
        if ($this->historicoSituacaoAlunos->contains($historicoSituacaoAluno) === false) {
            $this->historicoSituacaoAlunos[] = $historicoSituacaoAluno;
            $historicoSituacaoAluno->setAluno($this);
        }

        return $this;
    }

    public function removeHistoricoSituacaoAluno(HistoricoSituacaoAluno $historicoSituacaoAluno): self
    {
        if ($this->historicoSituacaoAlunos->contains($historicoSituacaoAluno) === true) {
            $this->historicoSituacaoAlunos->removeElement($historicoSituacaoAluno);
            // set the owning side to null (unless already changed)
            if ($historicoSituacaoAluno->getAluno() === $this) {
                $historicoSituacaoAluno->setAluno(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Servico[]
     */
    public function getServicos(): Collection
    {
        return $this->servicos;
    }

    public function addServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === false) {
            $this->servicos[] = $servico;
            $servico->setAluno($this);
        }

        return $this;
    }

    public function removeServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === true) {
            $this->servicos->removeElement($servico);
            // set the owning side to null (unless already changed)
            if ($servico->getAluno() === $this) {
                $servico->setAluno(null);
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
            $reposicaoAula->setAluno($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getAluno() === $this) {
                $reposicaoAula->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DescontoSuperAmigo[]
     */
    public function getDescontoSuperAmigos(): Collection
    {
        return $this->descontoSuperAmigos;
    }

    public function addDescontoSuperAmigo(DescontoSuperAmigo $descontoSuperAmigo): self
    {
        if ($this->descontoSuperAmigos->contains($descontoSuperAmigo) === false) {
            $this->descontoSuperAmigos[] = $descontoSuperAmigo;
            $descontoSuperAmigo->setAluno($this);
        }

        return $this;
    }

    public function removeDescontoSuperAmigo(DescontoSuperAmigo $descontoSuperAmigo): self
    {
        if ($this->descontoSuperAmigos->contains($descontoSuperAmigo) === true) {
            $this->descontoSuperAmigos->removeElement($descontoSuperAmigo);
            // set the owning side to null (unless already changed)
            if ($descontoSuperAmigo->getAluno() === $this) {
                $descontoSuperAmigo->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoDiarioPersonal[]
     */
    public function getAlunoDiarioPersonals(): Collection
    {
        return $this->alunoDiarioPersonals;
    }

    public function addAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === false) {
            $this->alunoDiarioPersonals[] = $alunoDiarioPersonal;
            $alunoDiarioPersonal->setAluno($this);
        }

        return $this;
    }

    public function removeAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === true) {
            $this->alunoDiarioPersonals->removeElement($alunoDiarioPersonal);
            // set the owning side to null (unless already changed)
            if ($alunoDiarioPersonal->getAluno() === $this) {
                $alunoDiarioPersonal->setAluno(null);
            }
        }

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
            $alunosBonusClass->setAluno($this);
        }

        return $this;
    }

    public function removeAlunosBonusClass(AlunosBonusClass $alunosBonusClass): self
    {
        if ($this->alunosBonusClasses->contains($alunosBonusClass) === true) {
            $this->alunosBonusClasses->removeElement($alunosBonusClass);
            // set the owning side to null (unless already changed)
            if ($alunosBonusClass->getAluno() === $this) {
                $alunosBonusClass->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EmprestimoBiblioteca[]
     */
    public function getEmprestimoBibliotecas(): Collection
    {
        return $this->emprestimoBibliotecas;
    }

    public function addEmprestimoBiblioteca(EmprestimoBiblioteca $emprestimoBiblioteca): self
    {
        if ($this->emprestimoBibliotecas->contains($emprestimoBiblioteca) === false) {
            $this->emprestimoBibliotecas[] = $emprestimoBiblioteca;
            $emprestimoBiblioteca->setAluno($this);
        }

        return $this;
    }

    public function removeEmprestimoBiblioteca(EmprestimoBiblioteca $emprestimoBiblioteca): self
    {
        if ($this->emprestimoBibliotecas->contains($emprestimoBiblioteca) === true) {
            $this->emprestimoBibliotecas->removeElement($emprestimoBiblioteca);
            // set the owning side to null (unless already changed)
            if ($emprestimoBiblioteca->getAluno() === $this) {
                $emprestimoBiblioteca->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MovimentoDollar[]
     */
    public function getMovimentoDollars(): Collection
    {
        return $this->movimentoDollars;
    }

    public function addMovimentoDollar(MovimentoDollar $movimentoDollar): self
    {
        if ($this->movimentoDollars->contains($movimentoDollar) === false) {
            $this->movimentoDollars[] = $movimentoDollar;
            $movimentoDollar->setAluno($this);
        }

        return $this;
    }

    public function removeMovimentoDollar(MovimentoDollar $movimentoDollar): self
    {
        if ($this->movimentoDollars->contains($movimentoDollar) === true) {
            $this->movimentoDollars->removeElement($movimentoDollar);
            // set the owning side to null (unless already changed)
            if ($movimentoDollar->getAluno() === $this) {
                $movimentoDollar->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OcorrenciaAcademica[]
     */
    public function getOcorrenciaAcademicas(): Collection
    {
        return $this->ocorrenciaAcademicas;
    }

    public function addOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === false) {
            $this->ocorrenciaAcademicas[] = $ocorrenciaAcademica;
            $ocorrenciaAcademica->setAluno($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === true) {
            $this->ocorrenciaAcademicas->removeElement($ocorrenciaAcademica);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademica->getAluno() === $this) {
                $ocorrenciaAcademica->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Midia[]
     */
    public function getTipoVisibilidade(): Collection
    {
        return $this->tipo_visibilidade;
    }

    public function addTipoVisibilidade(Midia $tipoVisibilidade): self
    {
        if ($this->tipo_visibilidade->contains($tipoVisibilidade) === false) {
            $this->tipo_visibilidade[] = $tipoVisibilidade;
        }

        return $this;
    }

    public function removeTipoVisibilidade(Midia $tipoVisibilidade): self
    {
        if ($this->tipo_visibilidade->contains($tipoVisibilidade) === true) {
            $this->tipo_visibilidade->removeElement($tipoVisibilidade);
        }

        return $this;
    }



    /**
     * Get the value of updated_at
     *
     * @return \DateTime
     */
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @param \DateTime  $updated_at
     *
     * @return self
     */
    public function setUpdated_at(\DateTime $updated_at)
    {
        $this->updated_at = $updated_at;

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
     * @param string|null $sponteId
     */
    public function setSponteId(?string $sponteId)
    {
        $this->sponte_id = $sponteId;
        return $this;
    }
}
