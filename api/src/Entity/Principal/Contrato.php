<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ContratoRepository")
 */
class Contrato
{


    function __construct()
    {
        $this->data_contrato         = new \DateTime();
        $this->data_matricula        = new \DateTime();
        $this->data_inicio_contrato  = new \DateTime();
        $this->data_termino_contrato = new \DateTime();
        $this->data_cancelamento = new \DateTime();
        $this->situacao      = 'V';
        $this->tipo_contrato = 'M';
        $this->excluido      = false;
        $this->contratoContaReceber      = new ArrayCollection();
        $this->descontoSuperAmigos       = new ArrayCollection();
        $this->agendamentoPersonals      = new ArrayCollection();
        $this->alunoAvaliacaos           = new ArrayCollection();
        $this->alunoAvaliacaoConceituals = new ArrayCollection();
        $this->alunoDiarios         = new ArrayCollection();
        $this->movimentoDollars     = new ArrayCollection();
        $this->ocorrenciaAcademicas = new ArrayCollection();
        $this->chave_aceite = uniqid().uniqid();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="contratos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Turma", inversedBy="contratos")
     */
    private $turma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro", inversedBy="contratos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="contratosVendas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsavel_venda_funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="contratosCarteiras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsavel_carteira_funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa", inversedBy="contratos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsavel_financeiro_pessoa;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $codigo_matricula_importado;

    /**
     * @ORM\Column(type="integer", options={"comment":"É um sequencial por aluno. No front irá formar o número do contrato com ALUNO_ID/SEQUENCIA_CONTRATO EX: 23191/2  o número da frente é o ID do aluno, e o segundo a sequencia do contrato."})
     */
    private $sequencia_contrato;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"(V)igente, (E)ncerrado, (R)escindido, (C)ancelado, (T)rancado"})
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"(M)atricula, (R)ematrícula, (T)ransferencia de unidade"})
     */
    private $tipo_contrato;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_contrato;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_matricula;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_inicio_contrato;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_termino_contrato;

   /**
     * @ORM\Column(type="datetime")
     */
    private $data_cancelamento;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_aceite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $texto;

    /**
     * @ORM\Column(type="boolean")
     */
    private $excluido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Curso", inversedBy="contratosCursos")
     */
    private $curso;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="contratosFranqueadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaReceber", mappedBy="contrato")
     */
    private $contratoContaReceber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Semestre", inversedBy="contratosSemestre")
     * @ORM\JoinColumn(nullable=false)
     */
    private $semestre;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default":"0"})
     */
    private $bolsista = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $familiar_desconto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Convenio", inversedBy="contratos")
     */
    private $convenio_desconto;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motivo_cancelamento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\DescontoSuperAmigo", mappedBy="contrato")
     */
    private $descontoSuperAmigos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendamentoPersonal", mappedBy="contrato", orphanRemoval=true)
     */
    private $agendamentoPersonals;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\CreditosPersonal", mappedBy="contrato", orphanRemoval=true)
     */
    private $creditosPersonal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ModalidadeTurma", inversedBy="contratos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modalidade_turma;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="contrato")
     */
    private $alunoAvaliacaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="contrato")
     */
    private $alunoDiarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="contrato")
     */
    private $alunoAvaliacaoConceituals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoDollar", mappedBy="contrato")
     */
    private $movimentoDollars;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademica", mappedBy="contrato")
     */
    private $ocorrenciaAcademicas;

    /**
     * @var                       string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $chave_aceite;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getResponsavelVendaFuncionario(): ?Funcionario
    {
        return $this->responsavel_venda_funcionario;
    }

    public function setResponsavelVendaFuncionario(?Funcionario $responsavel_venda_funcionario): self
    {
        $this->responsavel_venda_funcionario = $responsavel_venda_funcionario;

        return $this;
    }

    public function getResponsavelCarteiraFuncionario(): ?Funcionario
    {
        return $this->responsavel_carteira_funcionario;
    }

    public function setResponsavelCarteiraFuncionario(?Funcionario $responsavel_carteira_funcionario): self
    {
        $this->responsavel_carteira_funcionario = $responsavel_carteira_funcionario;

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

    public function getCodigoMatriculaImportado(): ?string
    {
        return $this->codigo_matricula_importado;
    }

    public function setCodigoMatriculaImportado(?string $codigo_matricula_importado): self
    {
        $this->codigo_matricula_importado = $codigo_matricula_importado;

        return $this;
    }

    public function getSequenciaContrato(): ?int
    {
        return $this->sequencia_contrato;
    }

    public function setSequenciaContrato(int $sequencia_contrato): self
    {
        $this->sequencia_contrato = $sequencia_contrato;

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

    public function getTipoContrato(): ?string
    {
        return $this->tipo_contrato;
    }

    public function setTipoContrato(string $tipo_contrato): self
    {
        $this->tipo_contrato = $tipo_contrato;

        return $this;
    }

    public function getDataContrato(): ?\DateTimeInterface
    {
        return $this->data_contrato;
    }

    public function setDataContrato(\DateTimeInterface $data_contrato): self
    {
        $this->data_contrato = $data_contrato;

        return $this;
    }

    public function getDataMatricula(): ?\DateTimeInterface
    {
        return $this->data_matricula;
    }

    public function setDataMatricula(\DateTimeInterface $data_matricula): self
    {
        $this->data_matricula = $data_matricula;

        return $this;
    }

    public function getDataInicioContrato(): ?\DateTimeInterface
    {
        return $this->data_inicio_contrato;
    }

    public function setDataInicioContrato(\DateTimeInterface $data_inicio_contrato): self
    {
        $this->data_inicio_contrato = $data_inicio_contrato;

        return $this;
    }

    public function getDataTerminoContrato(): ?\DateTimeInterface
    {
        return $this->data_termino_contrato;
    }

    public function setDataTerminoContrato(\DateTimeInterface $data_termino_contrato): self
    {
        $this->data_termino_contrato = $data_termino_contrato;

        return $this;
    }

    public function getDataCancelamentoContrato(): ?\DateTimeInterface
    {
        return $this->data_cancelamento;
    }

    public function setDataCancelamentoContrato(\DateTimeInterface $data_cancelamento): self
    {
        $this->data_cancelamento = $data_cancelamento;

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

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): self
    {
        $this->texto = $texto;

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

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

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
     * @return Collection|ContaReceber[]
     */
    public function getContratoContaReceber(): Collection
    {
        return $this->contratoContaReceber;
    }

    public function addContratoContaReceber(ContaReceber $contratoContaReceber): self
    {
        if ($this->contratoContaReceber->contains($contratoContaReceber) === false) {
            $this->contratoContaReceber[] = $contratoContaReceber;
            $contratoContaReceber->setContrato($this);
        }

        return $this;
    }

    public function removeContratoContaReceber(ContaReceber $contratoContaReceber): self
    {
        if ($this->contratoContaReceber->contains($contratoContaReceber) === true) {
            $this->contratoContaReceber->removeElement($contratoContaReceber);
            // set the owning side to null (unless already changed)
            if ($contratoContaReceber->getContrato() === $this) {
                $contratoContaReceber->setContrato(null);
            }
        }
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

    public function getBolsista(): ?bool
    {
        return $this->bolsista;
    }

    public function setBolsista(bool $bolsista): self
    {
        $this->bolsista = $bolsista;

        return $this;
    }

    public function getFamiliarDesconto(): ?string
    {
        return $this->familiar_desconto;
    }

    public function setFamiliarDesconto(?string $familiar_desconto): self
    {
        $this->familiar_desconto = $familiar_desconto;

        return $this;
    }

    public function getConvenioDesconto(): ?Convenio
    {
        return $this->convenio_desconto;
    }

    public function setConvenioDesconto(?Convenio $convenio_desconto): self
    {
        $this->convenio_desconto = $convenio_desconto;

        return $this;
    }

    public function getMotivoCancelamento(): ?string
    {
        return $this->motivo_cancelamento;
    }

    public function setMotivoCancelamento(?string $motivo_cancelamento): self
    {
        $this->motivo_cancelamento = $motivo_cancelamento;

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
            $descontoSuperAmigo->setContrato($this);
        }

        return $this;
    }

    public function removeDescontoSuperAmigo(DescontoSuperAmigo $descontoSuperAmigo): self
    {
        if ($this->descontoSuperAmigos->contains($descontoSuperAmigo) === true) {
            $this->descontoSuperAmigos->removeElement($descontoSuperAmigo);
            // set the owning side to null (unless already changed)
            if ($descontoSuperAmigo->getContrato() === $this) {
                $descontoSuperAmigo->setContrato(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AgendamentoPersonal[]
     */
    public function getAgendamentoPersonals(): Collection
    {
        return $this->agendamentoPersonals;
    }

    public function addAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === false) {
            $this->agendamentoPersonals[] = $agendamentoPersonal;
            $agendamentoPersonal->setContrato($this);
        }

        return $this;
    }
    public function removeAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === true) {
            $this->agendamentoPersonals->removeElement($agendamentoPersonal);
            // set the owning side to null (unless already changed)
            if ($agendamentoPersonal->getContrato() === $this) {
                $agendamentoPersonal->setContrato(null);
            }
        }

        return $this;
    }

    public function getCreditosPersonal(): ?CreditosPersonal
    {
        return $this->creditosPersonal;
    }

    public function setCreditosPersonal(CreditosPersonal $creditosPersonal): self
    {
        $this->creditosPersonal = $creditosPersonal;

        // set the owning side of the relation if necessary
        if ($this !== $creditosPersonal->getContrato()) {
            $creditosPersonal->setContrato($this);
        }

        return $this;
    }

    public function getModalidadeTurma(): ?ModalidadeTurma
    {
        return $this->modalidade_turma;
    }

    public function setModalidadeTurma(?ModalidadeTurma $modalidade_turma): self
    {
        $this->modalidade_turma = $modalidade_turma;

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
            $alunoAvaliacao->setContrato($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === true) {
            $this->alunoAvaliacaos->removeElement($alunoAvaliacao);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacao->getContrato() === $this) {
                $alunoAvaliacao->setContrato(null);
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
            $alunoDiario->setContrato($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getContrato() === $this) {
                $alunoDiario->setContrato(null);
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
            $alunoAvaliacaoConceitual->setContrato($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === true) {
            $this->alunoAvaliacaoConceituals->removeElement($alunoAvaliacaoConceitual);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitual->getContrato() === $this) {
                $alunoAvaliacaoConceitual->setContrato(null);
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
            $movimentoDollar->setContrato($this);
        }

        return $this;
    }

    public function removeMovimentoDollar(MovimentoDollar $movimentoDollar): self
    {
        if ($this->movimentoDollars->contains($movimentoDollar) === true) {
            $this->movimentoDollars->removeElement($movimentoDollar);
            // set the owning side to null (unless already changed)
            if ($movimentoDollar->getContrato() === $this) {
                $movimentoDollar->setContrato(null);
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
            $ocorrenciaAcademica->setContrato($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === true) {
            $this->ocorrenciaAcademicas->removeElement($ocorrenciaAcademica);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademica->getContrato() === $this) {
                $ocorrenciaAcademica->setContrato(null);
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
     * Get the value of data_aceite
     */ 
    public function getData_aceite()
    {
        return $this->data_aceite;
    }

    /**
     * Set the value of data_aceite
     *
     * @return  self
     */ 
    public function setData_aceite($data_aceite)
    {
        $this->data_aceite = $data_aceite;

        return $this;
    }

    /**
     * Get the value of chave_aceite
     */ 
    public function getChave_aceite()
    {
        return $this->chave_aceite;
    }

    /**
     * Set the value of chave_aceite
     *
     * @return  self
     */ 
    public function setChave_aceite($chave_aceite)
    {
        $this->chave_aceite = $chave_aceite;

        return $this;
    }
}
