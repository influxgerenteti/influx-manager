<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FuncionarioRepository")
 */
class Funcionario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa", inversedBy="funcionarios", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $pessoa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Cargo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cargo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\NivelInstrutor")
     */
    private $nivel_instrutor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Banco")
     * @ORM\JoinColumn(nullable=false)
     */
    private $banco;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario")
     */
    private $gestor_comercial_funcionario;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"H - Horista, M - Mensalista"})
     */
    private $tipo_pagamento;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $apelido;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_admissao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_demissao;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $agencia;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $digito_agencia;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $conta_corrente;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $digito_conta_corrente;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $recebe_emails = false;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $instrutor = false;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $instrutor_personal = false;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $gestor_comercial = true;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $consultor = true;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $atendente = true;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = "A";

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FuncionarioDisponibilidade", mappedBy="funcionario")
     */
    private $disponibilidades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="responsavel_venda_funcionario")
     */
    private $contratosVendas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="responsavel_carteira_funcionario")
     */
    private $contratosCarteiras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaReceber", mappedBy="vendedor_funcionario")
     */
    private $vendedorContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="consultor_funcionario")
     */
    private $consultorInteressados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="consultor_responsavel_funcionario")
     */
    private $consultorResponsavelInteressados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FuncionarioValorHora", mappedBy="funcionario")
     */
    private $funcionarioValorHoras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Convenio", mappedBy="consultor_funcionario")
     */
    private $convenios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademica", mappedBy="funcionario")
     */
    private $ocorrenciaAcademicas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="funcionario")
     */
    private $alunoDiarios;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="funcionarios")
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Turma", mappedBy="funcionario")
     */
    private $turmas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TurmaAula", mappedBy="funcionario")
     */
    private $turmaAulasDadas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Servico", mappedBy="funcionario")
     */
    private $servicos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ServicoHistorico", mappedBy="funcionario")
     */
    private $servicoHistoricos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademicaDetalhes", mappedBy="funcionario")
     */
    private $ocorrenciaAcademicaDetalhes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="responsaveis_execucacao")
     */
    private $atividadeExtrasPendentes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="responsavel_execucao")
     */
    private $responsavelReposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoFuncionario", mappedBy="funcionario")
     */
    private $pagamentoFuncionarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiarioPersonal", mappedBy="funcionario")
     */
    private $alunoDiarioPersonals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendamentoPersonal", mappedBy="funcionario")
     */
    private $agendamentoPersonals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="consultor_funcionario")
     */
    private $followupComercials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="funcionario")
     */
    private $agendaCompromissos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\BonusClass", mappedBy="funcionario")
     */
    private $bonusClasses;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $coordenador_pedagogico = false;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;

    public function __construct()
    {
        $this->disponibilidades      = new ArrayCollection();
        $this->contratosVendas       = new ArrayCollection();
        $this->contratosCarteiras    = new ArrayCollection();
        $this->vendedorContaReceber  = new ArrayCollection();
        $this->consultorInteressados = new ArrayCollection();
        $this->consultorResponsavelInteressados = new ArrayCollection();
        $this->funcionarioValorHoras            = new ArrayCollection();
        $this->turmas            = new ArrayCollection();
        $this->turmaAulasDadas   = new ArrayCollection();
        $this->servicos          = new ArrayCollection();
        $this->servicoHistoricos = new ArrayCollection();
        $this->ocorrenciaAcademicas = new ArrayCollection();
        $this->convenios            = new ArrayCollection();
        $this->alunoDiarios         = new ArrayCollection();
        $this->ocorrenciaAcademicaDetalhes = new ArrayCollection();
        $this->atividadeExtrasPendentes    = new ArrayCollection();
        $this->responsavelReposicaoAulas   = new ArrayCollection();
        $this->pagamentoFuncionarios       = new ArrayCollection();
        $this->alunoDiarioPersonals        = new ArrayCollection();
        $this->agendamentoPersonals        = new ArrayCollection();
        $this->followupComercials          = new ArrayCollection();
        $this->agendaCompromissos          = new ArrayCollection();
        $this->bonusClasses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(?Pessoa $pessoa): self
    {
        $this->pessoa = $pessoa;

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

    public function getCargo(): ?Cargo
    {
        return $this->cargo;
    }

    public function setCargo(?Cargo $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    public function getNivelInstrutor(): ?NivelInstrutor
    {
        return $this->nivel_instrutor;
    }

    public function setNivelInstrutor(?NivelInstrutor $nivel_instrutor): self
    {
        $this->nivel_instrutor = $nivel_instrutor;

        return $this;
    }

    public function getBanco(): ?Banco
    {
        return $this->banco;
    }

    public function setBanco(?Banco $banco): self
    {
        $this->banco = $banco;

        return $this;
    }

    public function getGestorComercialFuncionario(): ?self
    {
        return $this->gestor_comercial_funcionario;
    }

    public function setGestorComercialFuncionario(?self $gestor_comercial_funcionario): self
    {
        $this->gestor_comercial_funcionario = $gestor_comercial_funcionario;

        return $this;
    }

    public function getTipoPagamento(): ?string
    {
        return $this->tipo_pagamento;
    }

    public function setTipoPagamento(string $tipo_pagamento): self
    {
        $this->tipo_pagamento = $tipo_pagamento;

        return $this;
    }

    public function getApelido(): ?string
    {
        return $this->apelido;
    }

    public function setApelido(string $apelido): self
    {
        $this->apelido = $apelido;

        return $this;
    }

    public function getDataAdmissao(): ?\DateTimeInterface
    {
        return $this->data_admissao;
    }

    public function setDataAdmissao(?\DateTimeInterface $data_admissao): self
    {
        $this->data_admissao = $data_admissao;

        return $this;
    }

    public function getDataDemissao(): ?\DateTimeInterface
    {
        return $this->data_demissao;
    }

    public function setDataDemissao(?\DateTimeInterface $data_demissao): self
    {
        $this->data_demissao = $data_demissao;

        return $this;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(?string $agencia): self
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getDigitoAgencia(): ?string
    {
        return $this->digito_agencia;
    }

    public function setDigitoAgencia(?string $digito_agencia): self
    {
        $this->digito_agencia = $digito_agencia;

        return $this;
    }

    public function getContaCorrente(): ?string
    {
        return $this->conta_corrente;
    }

    public function setContaCorrente(?string $conta_corrente): self
    {
        $this->conta_corrente = $conta_corrente;

        return $this;
    }

    public function getDigitoContaCorrente(): ?string
    {
        return $this->digito_conta_corrente;
    }

    public function setDigitoContaCorrente(?string $digito_conta_corrente): self
    {
        $this->digito_conta_corrente = $digito_conta_corrente;

        return $this;
    }

    public function getRecebeEmails(): ?bool
    {
        return $this->recebe_emails;
    }

    public function setRecebeEmails(bool $recebe_emails): self
    {
        $this->recebe_emails = $recebe_emails;

        return $this;
    }

    public function getInstrutor(): ?bool
    {
        return $this->instrutor;
    }

    public function setInstrutor(bool $instrutor): self
    {
        $this->instrutor = $instrutor;

        return $this;
    }

    public function getInstrutorPersonal(): ?bool
    {
        return $this->instrutor_personal;
    }

    public function setInstrutorPersonal(bool $instrutor_personal): self
    {
        $this->instrutor_personal = $instrutor_personal;

        return $this;
    }

    public function getGestorComercial(): ?bool
    {
        return $this->gestor_comercial;
    }

    public function setGestorComercial(bool $gestor_comercial): self
    {
        $this->gestor_comercial = $gestor_comercial;

        return $this;
    }

    public function getConsultor(): ?bool
    {
        return $this->consultor;
    }

    public function setConsultor(bool $consultor): self
    {
        $this->consultor = $consultor;

        return $this;
    }

    public function getAtendente(): ?bool
    {
        return $this->atendente;
    }

    public function setAtendente(bool $atendente): self
    {
        $this->atendente = $atendente;

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
     *
     * @return Collection|FuncionarioDisponibilidade[]
     */
    public function getDisponibilidades(): Collection
    {
        return $this->disponibilidades;
    }

    public function addDisponibilidade(FuncionarioDisponibilidade $disponibilidade): self
    {
        if ($this->disponibilidades->contains($disponibilidade) === false) {
            $this->disponibilidades[] = $disponibilidade;
            $disponibilidade->setFuncionario($this);
        }

        return $this;
    }

    public function removeDisponibilidade(FuncionarioDisponibilidade $disponibilidade): self
    {
        if ($this->disponibilidades->contains($disponibilidade) === true) {
            $this->disponibilidades->removeElement($disponibilidade);
            // set the owning side to null (unless already changed)
            if ($disponibilidade->getFuncionario() === $this) {
                $disponibilidade->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratosVendas(): Collection
    {
        return $this->contratosVendas;
    }

    public function addContratosVenda(Contrato $contratosVenda): self
    {
        if ($this->contratosVendas->contains($contratosVenda) === false) {
            $this->contratosVendas[] = $contratosVenda;
            $contratosVenda->setResponsavelVendaFuncionario($this);
        }

        return $this;
    }

    public function removeContratosVenda(Contrato $contratosVenda): self
    {
        if ($this->contratosVendas->contains($contratosVenda) === true) {
            $this->contratosVendas->removeElement($contratosVenda);
            // set the owning side to null (unless already changed)
            if ($contratosVenda->getResponsavelVendaFuncionario() === $this) {
                $contratosVenda->setResponsavelVendaFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratosCarteiras(): Collection
    {
        return $this->contratosCarteiras;
    }

    public function addContratosCarteira(Contrato $contratosCarteira): self
    {
        if ($this->contratosCarteiras->contains($contratosCarteira) === false) {
            $this->contratosCarteiras[] = $contratosCarteira;
            $contratosCarteira->setResponsavelCarteiraFuncionario($this);
        }

        return $this;
    }

    public function removeContratosCarteira(Contrato $contratosCarteira): self
    {
        if ($this->contratosCarteiras->contains($contratosCarteira) === true) {
            $this->contratosCarteiras->removeElement($contratosCarteira);
            // set the owning side to null (unless already changed)
            if ($contratosCarteira->getResponsavelCarteiraFuncionario() === $this) {
                $contratosCarteira->setResponsavelCarteiraFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Interessado[]
     */
    public function getConsultorInteressados(): Collection
    {
        return $this->consultorInteressados;
    }

    public function addConsultorInteressado(Interessado $consultorInteressado): self
    {
        if ($this->consultorInteressados->contains($consultorInteressado) === false) {
            $this->consultorInteressados[] = $consultorInteressado;
            $consultorInteressado->setConsultorFuncionario($this);
        }

        return $this;
    }

    public function removeConsultorInteressado(Interessado $consultorInteressado): self
    {
        if ($this->consultorInteressados->contains($consultorInteressado) === true) {
            $this->consultorInteressados->removeElement($consultorInteressado);
            // set the owning side to null (unless already changed)
            if ($consultorInteressado->getConsultorFuncionario() === $this) {
                $consultorInteressado->setConsultorFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Interessado[]
     */
    public function getConsultorResponsavelInteressados(): Collection
    {
        return $this->consultorResponsavelInteressados;
    }

    public function addConsultorResponsavelInteressado(Interessado $consultorResponsavelInteressado): self
    {
        if ($this->consultorResponsavelInteressados->contains($consultorResponsavelInteressado) === false) {
            $this->consultorResponsavelInteressados[] = $consultorResponsavelInteressado;
            $consultorResponsavelInteressado->setConsultorResponsavelFuncionario($this);
        }

        return $this;
    }

    public function removeConsultorResponsavelInteressado(Interessado $consultorResponsavelInteressado): self
    {
        if ($this->consultorResponsavelInteressados->contains($consultorResponsavelInteressado) === true) {
            $this->consultorResponsavelInteressados->removeElement($consultorResponsavelInteressado);
            // set the owning side to null (unless already changed)
            if ($consultorResponsavelInteressado->getConsultorResponsavelFuncionario() === $this) {
                $consultorResponsavelInteressado->setConsultorResponsavelFuncionario(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|ContaReceber[]
     */
    public function getVendedorContaReceber(): Collection
    {
        return $this->vendedorContaReceber;
    }

    public function addVendedorContaReceber(ContaReceber $vendedorContaReceber): self
    {
        if ($this->vendedorContaReceber->contains($vendedorContaReceber) === false) {
            $this->vendedorContaReceber[] = $vendedorContaReceber;
            $vendedorContaReceber->setVendedorPessoa($this);
        }

        return $this;
    }

    public function removeVendedorContaReceber(ContaReceber $vendedorContaReceber): self
    {
        if ($this->vendedorContaReceber->contains($vendedorContaReceber) === true) {
            $this->vendedorContaReceber->removeElement($vendedorContaReceber);
            // set the owning side to null (unless already changed)
            if ($vendedorContaReceber->getVendedorPessoa() === $this) {
                $vendedorContaReceber->setVendedorPessoa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FuncionarioValorHora[]
     */
    public function getFuncionarioValorHoras(): Collection
    {
        return $this->funcionarioValorHoras;
    }

    public function addFuncionarioValorHora(FuncionarioValorHora $funcionarioValorHora): self
    {
        if ($this->funcionarioValorHoras->contains($funcionarioValorHora) === false) {
            $this->funcionarioValorHoras[] = $funcionarioValorHora;
            $funcionarioValorHora->setFuncionario($this);
        }

        return $this;
    }

    public function removeFuncionarioValorHora(FuncionarioValorHora $funcionarioValorHora): self
    {
        if ($this->funcionarioValorHoras->contains($funcionarioValorHora) === true) {
            $this->funcionarioValorHoras->removeElement($funcionarioValorHora);
            // set the owning side to null (unless already changed)
            if ($funcionarioValorHora->getFuncionario() === $this) {
                $funcionarioValorHora->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Convenio[]
     */
    public function getConvenios(): Collection
    {
        return $this->convenios;
    }

    public function addConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === false) {
            $this->convenios[] = $convenio;
            $convenio->setConsultorFuncionario($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === true) {
            $this->convenios->removeElement($convenio);
            // set the owning side to null (unless already changed)
            if ($convenio->getConsultorFuncionario() === $this) {
                $convenio->setConsultorFuncionario(null);
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
            $ocorrenciaAcademica->setFuncionario($this);
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
            $alunoDiario->setFuncionario($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === true) {
            $this->ocorrenciaAcademicas->removeElement($ocorrenciaAcademica);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademica->getFuncionario() === $this) {
                $ocorrenciaAcademica->setFuncionario(null);
            }
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getFuncionario() === $this) {
                $alunoDiario->setFuncionario(null);
            }
        }

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection|Turma[]
     */
    public function getTurmas(): Collection
    {
        return $this->turmas;
    }

    public function addTurma(Turma $turma): self
    {
        if ($this->turmas->contains($turma) === false) {
            $this->turmas[] = $turma;
            $turma->setFuncionario($this);
        }

        return $this;
    }

    public function removeTurma(Turma $turma): self
    {
        if ($this->turmas->contains($turma) === true) {
            $this->turmas->removeElement($turma);
            // set the owning side to null (unless already changed)
            if ($turma->getFuncionario() === $this) {
                $turma->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TurmaAula[]
     */
    public function getTurmaAulasDadas(): Collection
    {
        return $this->turmaAulasDadas;
    }

    public function addTurmaAulasDada(TurmaAula $turmaAulasDada): self
    {
        if ($this->turmaAulasDadas->contains($turmaAulasDada) === false) {
            $this->turmaAulasDadas[] = $turmaAulasDada;
            $turmaAulasDada->setFuncionario($this);
        }

        return $this;
    }

    public function removeTurmaAulasDada(TurmaAula $turmaAulasDada): self
    {
        if ($this->turmaAulasDadas->contains($turmaAulasDada) === true) {
            $this->turmaAulasDadas->removeElement($turmaAulasDada);
            // set the owning side to null (unless already changed)
            if ($turmaAulasDada->getFuncionario() === $this) {
                $turmaAulasDada->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
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
            $servico->setFuncionario($this);
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
            $ocorrenciaAcademicaDetalhe->setFuncionario($this);
        }

        return $this;
    }

    public function removeServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === true) {
            $this->servicos->removeElement($servico);
            // set the owning side to null (unless already changed)
            if ($servico->getFuncionario() === $this) {
                $servico->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServicoHistorico[]
     */
    public function getServicoHistoricos(): Collection
    {
        return $this->servicoHistoricos;
    }

    public function addServicoHistorico(ServicoHistorico $servicoHistorico): self
    {
        if ($this->servicoHistoricos->contains($servicoHistorico) === false) {
            $this->servicoHistoricos[] = $servicoHistorico;
            $servicoHistorico->setFuncionario($this);
        }

        return $this;
    }

    public function removeServicoHistorico(ServicoHistorico $servicoHistorico): self
    {
        if ($this->servicoHistoricos->contains($servicoHistorico) === true) {
            $this->servicoHistoricos->removeElement($servicoHistorico);
            // set the owning side to null (unless already changed)
            if ($servicoHistorico->getFuncionario() === $this) {
                $servicoHistorico->setFuncionario(null);
            }

            return $this;
        }
    }

    public function removeOcorrenciaAcademicaDetalhe(OcorrenciaAcademicaDetalhes $ocorrenciaAcademicaDetalhe): self
    {
        if ($this->ocorrenciaAcademicaDetalhes->contains($ocorrenciaAcademicaDetalhe) === true) {
            $this->ocorrenciaAcademicaDetalhes->removeElement($ocorrenciaAcademicaDetalhe);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademicaDetalhe->getFuncionario() === $this) {
                $ocorrenciaAcademicaDetalhe->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AtividadeExtra[]
     */
    public function getAtividadeExtrasPendentes(): Collection
    {
        return $this->atividadeExtrasPendentes;
    }

    public function addAtividadeExtrasPendente(AtividadeExtra $atividadeExtrasPendente): self
    {
        if ($this->atividadeExtrasPendentes->contains($atividadeExtrasPendente) === false) {
            $this->atividadeExtrasPendentes[] = $atividadeExtrasPendente;
            $atividadeExtrasPendente->addResponsaveisExecucacao($this);
        }

        return $this;
    }

    public function removeAtividadeExtrasPendente(AtividadeExtra $atividadeExtrasPendente): self
    {
        if ($this->atividadeExtrasPendentes->contains($atividadeExtrasPendente) === true) {
            $this->atividadeExtrasPendentes->removeElement($atividadeExtrasPendente);
            $atividadeExtrasPendente->removeResponsaveisExecucacao($this);
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getResponsavelReposicaoAulas(): Collection
    {
        return $this->responsavelReposicaoAulas;
    }

    public function addResponsavelReposicaoAula(ReposicaoAula $responsavelReposicaoAula): self
    {
        if ($this->responsavelReposicaoAulas->contains($responsavelReposicaoAula) === false) {
            $this->responsavelReposicaoAulas[] = $responsavelReposicaoAula;
            $responsavelReposicaoAula->setResponsavelExecucao($this);
        }

        return $this;
    }

    public function removeResponsavelReposicaoAula(ReposicaoAula $responsavelReposicaoAula): self
    {
        if ($this->responsavelReposicaoAulas->contains($responsavelReposicaoAula) === true) {
            $this->responsavelReposicaoAulas->removeElement($responsavelReposicaoAula);
            // set the owning side to null (unless already changed)
            if ($responsavelReposicaoAula->getResponsavelExecucao() === $this) {
                $responsavelReposicaoAula->setResponsavelExecucao(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoFuncionario[]
     */
    public function getPagamentoFuncionarios(): Collection
    {
        return $this->pagamentoFuncionarios;
    }

    public function addPagamentoFuncionario(PagamentoFuncionario $pagamentoFuncionario): self
    {
        if ($this->pagamentoFuncionarios->contains($pagamentoFuncionario) === false) {
            $this->pagamentoFuncionarios[] = $pagamentoFuncionario;
            $pagamentoFuncionario->setFuncionario($this);
        }

        return $this;
    }

    public function removePagamentoFuncionario(PagamentoFuncionario $pagamentoFuncionario): self
    {
        if ($this->pagamentoFuncionarios->contains($pagamentoFuncionario) === true) {
            $this->pagamentoFuncionarios->removeElement($pagamentoFuncionario);
            // set the owning side to null (unless already changed)
            if ($pagamentoFuncionario->getFuncionario() === $this) {
                $pagamentoFuncionario->setFuncionario(null);
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
            $alunoDiarioPersonal->setFuncionario($this);
        }

        return $this;
    }

    /**
     * @return Collection|FollowupComercial[]
     */
    public function getFollowupComercials(): Collection
    {
        return $this->followupComercials;
    }

    public function addFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === false) {
            $this->followupComercials[] = $followupComercial;
            $followupComercial->setConsultorFuncionario($this);
        }

        return $this;
    }

    public function removeAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === true) {
            $this->alunoDiarioPersonals->removeElement($alunoDiarioPersonal);
            // set the owning side to null (unless already changed)
            if ($alunoDiarioPersonal->getFuncionario() === $this) {
                $alunoDiarioPersonal->setFuncionario(null);
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
            $agendamentoPersonal->setFuncionario($this);
        }

        return $this;
    }

    public function removeAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === true) {
            $this->agendamentoPersonals->removeElement($agendamentoPersonal);
            // set the owning side to null (unless already changed)
            if ($agendamentoPersonal->getFuncionario() === $this) {
                $agendamentoPersonal->setFuncionario(null);
            }
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getConsultorFuncionario() === $this) {
                $followupComercial->setConsultorFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AgendaCompromisso[]
     */
    public function getAgendaCompromissos(): Collection
    {
        return $this->agendaCompromissos;
    }

    public function addAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === false) {
            $this->agendaCompromissos[] = $agendaCompromisso;
            $agendaCompromisso->setFuncionario($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getFuncionario() === $this) {
                $agendaCompromisso->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BonusClass[]
     */
    public function getBonusClasses(): Collection
    {
        return $this->bonusClasses;
    }

    public function addBonusClass(BonusClass $bonusClass): self
    {
        if ($this->bonusClasses->contains($bonusClass) === false) {
            $this->bonusClasses[] = $bonusClass;
            $bonusClass->setFuncionario($this);
        }

        return $this;
    }

    public function removeBonusClass(BonusClass $bonusClass): self
    {
        if ($this->bonusClasses->contains($bonusClass) === true) {
            $this->bonusClasses->removeElement($bonusClass);
            // set the owning side to null (unless already changed)
            if ($bonusClass->getFuncionario() === $this) {
                $bonusClass->setFuncionario(null);
            }
        }

        return $this;
    }

    public function getCoordenadorPedagogico(): ?bool
    {
        return $this->coordenador_pedagogico;
    }

    public function setCoordenadorPedagogico(bool $coordenador_pedagogico): self
    {
        $this->coordenador_pedagogico = $coordenador_pedagogico;

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


}
