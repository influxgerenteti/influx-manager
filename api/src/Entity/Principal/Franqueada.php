<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FranqueadaRepository")
 */
class Franqueada
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
     * @ORM\Column(type="string", length=80)
     */
    private $nome;

    /**
     *
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $razao_social;

    /**
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $endereco;

    /**
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $inscricao_estadual;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_secundario;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     *
     * @ORM\Column(type="string", length=14)
     */
    private $cnpj;

    /**
     *
     * @ORM\Column(type="integer", options={"default":"0"})
     */
    private $dias_em_abertos_movimentos = 0;

    /**
     *
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $sabado_dia_util = 1;

    /**
     *
     * @ORM\Column(type="string", length=2, options={"comment":"A - Ativo, I - Inativo, R - Removido", "default":"A"})
     */
    private $situacao = 'A';

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoMovimentoConta")
     */
    private $tipo_movimento_conta_receber;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoMovimentoConta")
     */
    private $tipo_movimento_conta_pagar;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaPagar", mappedBy="franqueada")
     */
    private $contasPagar;

    /**
     *
     * @ORM\ManyToMany(targetEntity="\App\Entity\Principal\Pessoa", mappedBy="franqueadas")
     */
    private $pessoasFranqueada;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_direcao;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_comercial;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Usuario", mappedBy="franqueadas")
     */
    private $usuarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cheque", mappedBy="franqueada")
     */
    private $cheques;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Turma", mappedBy="franqueada")
     */
    private $turmas;
 
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\SalaAgendamentoPersonal", mappedBy="franqueada")
     */
    private $salaAgendamentoPersonal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PlanoConta", mappedBy="franqueada")
     */
    private $planoContas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="franqueada")
     */
    private $contratosFranqueadas;

    /**
     * @ORM\Column(type="boolean", options={"comment":"Esta franqueada é a franqueadora ou não"})
     */
    private $franqueadora;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaReceber", mappedBy="franqueada")
     */
    private $franqueadaContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Boleto", mappedBy="franqueada")
     */
    private $boletos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TransacaoCartao", mappedBy="franqueada")
     */
    private $transacaoCartaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="franqueada")
     */
    private $franqueadaInteressados;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $percentual_multa;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=4)
     */
    private $percentual_juro_dia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="franqueada")
     */
    private $tituloRecebers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\DiasSubsequentes", mappedBy="franqueada")
     */
    private $diasSubsequentes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Conta", cascade={"persist", "remove"})
     */
    private $conta_padrao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Recibo", mappedBy="franqueada")
     */
    private $recibosFranqueada;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero_recibo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Convenio", mappedBy="franqueada")
     */
    private $convenios;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Estado")
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Cidade")
     */
    private $cidade;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_endereco;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $bairro_endereco;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complemento_endereco;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $cep_endereco;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Notificacoes", mappedBy="franqueada")
     */
    private $notificacoes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OperadoraCartao", mappedBy="franqueada")
     */
    private $operadoraCartaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModeloTemplate", mappedBy="franqueada")
     */
    private $modeloTemplates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModeloTemplateFranqueada", mappedBy="franqueada")
     */
    private $modelo_template_franqueadas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ChecklistAtividade", mappedBy="franqueada")
     */
    private $checklistAtividades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ChecklistAtividadeRealizada", mappedBy="franqueada")
     */
    private $checklistAtividadeRealizadas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademica", mappedBy="franqueada")
     */
    private $ocorrenciaAcademicas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TurmaAula", mappedBy="franqueada")
     */
    private $turmaAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="franqueada")
     */
    private $alunoDiarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="franqueada")
     */
    private $alunoAvaliacaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="franqueada")
     */
    private $alunoAvaliacaoConceituals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Conta", mappedBy="franqueada")
     */
    private $contas;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="franqueada")
     */
    private $atividadeExtras;
    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Servico", mappedBy="franqueada")
     */
    private $servicos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="franqueada")
     */
    private $reposicaoAulas;

    /**
     * @ORM\Column(type="integer", options={"comment":"Marcar como negativado, após alguma parcela estiver vencida por X dias", "default":"90"})
     */
    private $dias_para_negativacao = 90;

    /**
     * @ORM\Column(type="integer", options={"comment":"Define a quantidade de dias para envio de e-mail de lembrete de vencimento.","default":"2"})
     */
    private $dias_lembrete_cobranca = 2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoFuncionario", mappedBy="franqueada")
     */
    private $pagamentoFuncionarios;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $desconto_super_amigos_ativo = true;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $desconto_super_amigos_turbinado_ativo = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="franqueada")
     */
    private $agendaCompromissos;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\LivroBiblioteca", mappedBy="franqueada")
     */
    private $livroBibliotecas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\EmprestimoBiblioteca", mappedBy="franqueada")
     */
    private $emprestimoBibliotecas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Calendario", mappedBy="franqueada")
     */
    private $calendarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\BonusClass", mappedBy="franqueada", orphanRemoval=true)
     */
    private $bonusClasses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TipoVisibilidade", mappedBy="franqueada")
     */
    private $tipoVisibilidades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoItem", mappedBy="franqueada")
     */
    private $historicoItems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MetaFranqueada", mappedBy="franqueada")
     */
    private $metaFranqueadas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MetaFranqueadaHistorico", mappedBy="franqueada", orphanRemoval=true)
     */
    private $metaFranqueadaHistoricos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ConvidadoAtividadeExtra", mappedBy="franqueada")
     */
    private $convidadoAtividadeExtras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TransferenciaBancaria", mappedBy="franqueada", orphanRemoval=true)
     */
    private $transferenciaBancarias;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $limite_dias_alteracao_documento;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $percentual_desconto_a_vista;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PesquisaVisibilidade", mappedBy="franqueada")
     */
    private $pesquisaVisibilidades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Midia", mappedBy="franqueada")
     */
    private $midias;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MidiaFranqueada", mappedBy="franqueada")
     */
    private $midiaFranqueadas;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $excluido = 0;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updated_at;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemFranqueada", mappedBy="franqueada", orphanRemoval=true)
     */
    private $itemFranqueadas;


    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;

    public function __construct()
    {
        $this->contasPagar       = new ArrayCollection();
        $this->pessoasFranqueada = new ArrayCollection();
        $this->usuarios          = new ArrayCollection();
        $this->cheques           = new ArrayCollection();
        $this->turmas            = new ArrayCollection();
        $this->salaAgendamentoPersonal            = new ArrayCollection();
        $this->planoContas       = new ArrayCollection();
        $this->contratosFranqueadas   = new ArrayCollection();
        $this->franqueadora           = false;
        $this->numero_recibo          = 0;
        $this->franqueadaContaReceber = new ArrayCollection();
        $this->boletos          = new ArrayCollection();
        $this->transacaoCartaos = new ArrayCollection();
        $this->franqueadaInteressados = new ArrayCollection();
        $this->tituloRecebers         = new ArrayCollection();
        $this->diasSubsequentes       = new ArrayCollection();
        $this->recibosFranqueada      = new ArrayCollection();
        $this->notificacoes           = new ArrayCollection();
        $this->convenios        = new ArrayCollection();
        $this->operadoraCartaos = new ArrayCollection();
        $this->modelo_template_franqueadas = new ArrayCollection();
        $this->modeloTemplates     = new ArrayCollection();
        $this->checklistAtividades = new ArrayCollection();
        $this->checklistAtividadeRealizadas = new ArrayCollection();
        $this->ocorrenciaAcademicas         = new ArrayCollection();
        $this->turmaAulas      = new ArrayCollection();
        $this->alunoDiarios    = new ArrayCollection();
        $this->alunoAvaliacaos = new ArrayCollection();
        $this->alunoAvaliacaoConceituals = new ArrayCollection();
        $this->contas          = new ArrayCollection();
        $this->atividadeExtras = new ArrayCollection();
        $this->servicos        = new ArrayCollection();
        $this->reposicaoAulas  = new ArrayCollection();
        $this->pagamentoFuncionarios = new ArrayCollection();
        $this->livroBibliotecas      = new ArrayCollection();
        $this->emprestimoBibliotecas = new ArrayCollection();
        $this->calendarios           = new ArrayCollection();
        $this->bonusClasses          = new ArrayCollection();
        $this->agendaCompromissos    = new ArrayCollection();
        $this->tipoVisibilidades     = new ArrayCollection();
        $this->historicoItems        = new ArrayCollection();
        $this->metaFranqueadas       = new ArrayCollection();
        $this->metaFranqueadaHistoricos = new ArrayCollection();
        $this->convidadoAtividadeExtras = new ArrayCollection();
        $this->transferenciaBancarias   = new ArrayCollection();
        $this->pesquisaVisibilidades    = new ArrayCollection();
        $this->midias           = new ArrayCollection();
        $this->midiaFranqueadas = new ArrayCollection();
        $this->itemFranqueadas  = new ArrayCollection();

    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getRazaoSocial(): ?string
    {
        return $this->razao_social;
    }

    public function setRazaoSocial(?string $razao_social): self
    {
        $this->razao_social = $razao_social;

        return $this;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function setEndereco(?string $endereco): self
    {
        $this->endereco = $endereco;

        return $this;
    }

    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricao_estadual;
    }

    public function setInscricaoEstadual(?string $inscricao_estadual): self
    {
        $this->inscricao_estadual = $inscricao_estadual;

        return $this;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getTelefoneSecundario(): ?string
    {
        return $this->telefone_secundario;
    }

    public function setTelefoneSecundario(?string $telefone_secundario): self
    {
        $this->telefone_secundario = $telefone_secundario;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): self
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    public function getDiasEmAbertosMovimentos(): ?int
    {
        return $this->dias_em_abertos_movimentos;
    }

    public function setDiasEmAbertosMovimentos(int $dias_em_abertos_movimentos): self
    {
        $this->dias_em_abertos_movimentos = $dias_em_abertos_movimentos;

        return $this;
    }

    public function getSabadoDiaUtil(): ?bool
    {
        return $this->sabado_dia_util;
    }

    public function setSabadoDiaUtil(bool $sabado_dia_util): self
    {
        $this->sabado_dia_util = $sabado_dia_util;

        return $this;
    }

    public function getTipoMovimentoContaReceber(): ?TipoMovimentoConta
    {
        return $this->tipo_movimento_conta_receber;
    }

    public function setTipoMovimentoContaReceber(?TipoMovimentoConta $tipo_movimento_conta_receber): self
    {
        $this->tipo_movimento_conta_receber = $tipo_movimento_conta_receber;

        return $this;
    }

    public function getTipoMovimentoContaPagar(): ?TipoMovimentoConta
    {
        return $this->tipo_movimento_conta_pagar;
    }

    public function setTipoMovimentoContaPagar(?TipoMovimentoConta $tipo_movimento_conta_pagar): self
    {
        $this->tipo_movimento_conta_pagar = $tipo_movimento_conta_pagar;

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
     * @return Collection|ContaPagar[]
     */
    public function getContasPagar(): Collection
    {
        return $this->contasPagar;
    }

    public function addContaPagar(ContaPagar $contaPagar): self
    {
        if ($this->contasPagar->contains($contaPagar) === false) {
            $this->contasPagar[] = $contaPagar;
            $contaPagar->setFranqueada($this);
        }

        return $this;
    }

    public function removeContaPagar(ContaPagar $contaPagar): self
    {
        if ($this->contasPagar->contains($contaPagar) === true) {
            $this->contasPagar->removeElement($contaPagar);
            // set the owning side to null (unless already changed)
            if ($contaPagar->getFranqueada() === $this) {
                $contaPagar->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Pessoa[]
     */
    public function getPessoasFranqueada(): Collection
    {
        return $this->pessoasFranqueada;
    }

    public function addPessoasFranqueada(Pessoa $pessoa): self
    {
        if ($this->pessoasFranqueada->contains($pessoa) === false) {
            $this->pessoasFranqueada[] = $pessoa;
        }

        return $this;
    }

    public function removePessoasFranqueada(Pessoa $pessoa): self
    {
        if ($this->pessoasFranqueada->contains($pessoa) === true) {
            $this->pessoasFranqueada->removeElement($pessoa);
        }

        return $this;
    }

    public function getEmailDirecao(): ?string
    {
        return $this->email_direcao;
    }

    public function setEmailDirecao(string $email_direcao): self
    {
        $this->email_direcao = $email_direcao;

        return $this;
    }

    public function getEmailComercial(): ?string
    {
        return $this->email_comercial;
    }

    public function setEmailComercial(string $email_comercial): self
    {
        $this->email_comercial = $email_comercial;

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->contains($usuario) === false) {
            $this->usuarios[] = $usuario;
            $usuario->addFranqueada($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->contains($usuario) === true) {
            $this->usuarios->removeElement($usuario);
            $usuario->removeFranqueada($this);
        }

        return $this;
    }

    /**
     * @return Collection|Cheque[]
     */
    public function getCheques(): Collection
    {
        return $this->cheques;
    }

    public function addCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === false) {
            $this->cheques[] = $cheque;
            $cheque->setFranqueada($this);
        }

        return $this;
    }

    public function removeCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === true) {
            $this->cheques->removeElement($cheque);
            // set the owning side to null (unless already changed)
            if ($cheque->getFranqueada() === $this) {
                $cheque->setFranqueada(null);
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

    public function addTurma(Turma $turma): self
    {
        if ($this->turmas->contains($turma) === false) {
            $this->turmas[] = $turma;
            $turma->setFranqueada($this);
        }

        return $this;
    }

        /**
     * @return Collection|SalaAgendamentoPersonal[]
     */
    public function getSalaAgendamentoPersonal(): Collection
    {
        return $this->salaAgendamentoPersonal;
    }

    public function addSalaAgendamentoPersonal(SalaAgendamentoPersonal $salaAgendamentoPersonal): self
    {
        if ($this->salaAgendamentoPersonal->contains($salaAgendamentoPersonal) === false) {
            $this->salaAgendamentoPersonal[] = $salaAgendamentoPersonal;
            $salaAgendamentoPersonal->setFranqueada($this);
        }

        return $this;
    }

    /**
     * @return Collection|PlanoConta[]
     */
    public function getPlanoContas(): Collection
    {
        return $this->planoContas;
    }

    public function addPlanoConta(PlanoConta $planoConta): self
    {
        if ($this->planoContas->contains($planoConta) === false) {
            $this->planoContas[] = $planoConta;
            $planoConta->setFranqueada($this);
        }

        return $this;
    }

    public function removeTurma(Turma $turma): self
    {
        if ($this->turmas->contains($turma) === true) {
            $this->turmas->removeElement($turma);
            // set the owning side to null (unless already changed)
            if ($turma->getFranqueada() === $this) {
                $turma->setFranqueada(null);
            }
        }

        return $this;
    }

    public function removePlanoConta(PlanoConta $planoConta): self
    {
        if ($this->planoContas->contains($planoConta) === true) {
            $this->planoContas->removeElement($planoConta);
            // set the owning side to null (unless already changed)
            if ($planoConta->getFranqueada() === $this) {
                $planoConta->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratosFranqueadas(): Collection
    {
        return $this->contratosFranqueadas;
    }

    public function addContratosFranqueada(Contrato $contratosFranqueada): self
    {
        if ($this->contratosFranqueadas->contains($contratosFranqueada) === false) {
            $this->contratosFranqueadas[] = $contratosFranqueada;
            $contratosFranqueada->setFranqueada($this);
        }

        return $this;
    }

    public function removeContratosFranqueada(Contrato $contratosFranqueada): self
    {
        if ($this->contratosFranqueadas->contains($contratosFranqueada) === true) {
            $this->contratosFranqueadas->removeElement($contratosFranqueada);
            // set the owning side to null (unless already changed)
            if ($contratosFranqueada->getFranqueada() === $this) {
                $contratosFranqueada->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getFranqueadora(): ?bool
    {
        return $this->franqueadora;
    }

    public function setFranqueadora(bool $franqueadora): self
    {
        $this->franqueadora = $franqueadora;

        return $this;
    }

    /**
     * @return Collection|ContaReceber[]
     */
    public function getFranqueadaContaReceber(): Collection
    {
        return $this->franqueadaContaReceber;
    }

    public function addFranqueadaContaReceber(ContaReceber $franqueadaContaReceber): self
    {
        if ($this->franqueadaContaReceber->contains($franqueadaContaReceber) === false) {
            $this->franqueadaContaReceber[] = $franqueadaContaReceber;
            $franqueadaContaReceber->setFranqueada($this);
        }

        return $this;
    }

    public function removeFranqueadaContaReceber(ContaReceber $franqueadaContaReceber): self
    {
        if ($this->franqueadaContaReceber->contains($franqueadaContaReceber) === true) {
            $this->franqueadaContaReceber->removeElement($franqueadaContaReceber);
            // set the owning side to null (unless already changed)
            if ($franqueadaContaReceber->getFranqueada() === $this) {
                $franqueadaContaReceber->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Boleto[]
     */
    public function getBoletos(): Collection
    {
        return $this->boletos;
    }

    public function addBoleto(Boleto $boleto): self
    {
        if ($this->boletos->contains($boleto) === false) {
            $this->boletos[] = $boleto;
            $boleto->setFranqueada($this);
        }

        return $this;
    }

    public function removeBoleto(Boleto $boleto): self
    {
        if ($this->boletos->contains($boleto) === true) {
            $this->boletos->removeElement($boleto);
            // set the owning side to null (unless already changed)
            if ($boleto->getFranqueada() === $this) {
                $boleto->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TransacaoCartao[]
     */
    public function getTransacaoCartaos(): Collection
    {
        return $this->transacaoCartaos;
    }

    public function addTransacaoCartao(TransacaoCartao $transacaoCartao): self
    {
        if ($this->transacaoCartaos->contains($transacaoCartao) === false) {
            $this->transacaoCartaos[] = $transacaoCartao;
            $transacaoCartao->setFranqueada($this);
        }

        return $this;
    }

    public function removeTransacaoCartao(TransacaoCartao $transacaoCartao): self
    {
        if ($this->transacaoCartaos->contains($transacaoCartao) === true) {
            $this->transacaoCartaos->removeElement($transacaoCartao);
            // set the owning side to null (unless already changed)
            if ($transacaoCartao->getFranqueada() === $this) {
                $transacaoCartao->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Interessado[]
     */
    public function getFranqueadaInteressados(): Collection
    {
        return $this->franqueadaInteressados;
    }

    public function addFranqueadaInteressado(Interessado $franqueadaInteressado): self
    {
        if ($this->franqueadaInteressados->contains($franqueadaInteressado) === false) {
            $this->franqueadaInteressados[] = $franqueadaInteressado;
            $franqueadaInteressado->setFranqueada($this);
        }

        return $this;
    }

    public function removeFranqueadaInteressado(Interessado $franqueadaInteressado): self
    {
        if ($this->franqueadaInteressados->contains($franqueadaInteressado) === true) {
            $this->franqueadaInteressados->removeElement($franqueadaInteressado);
            // set the owning side to null (unless already changed)
            if ($franqueadaInteressado->getFranqueada() === $this) {
                $franqueadaInteressado->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getPercentualMulta()
    {
        return $this->percentual_multa;
    }

    public function setPercentualMulta($percentual_multa): self
    {
        $this->percentual_multa = $percentual_multa;

        return $this;
    }

    public function getPercentualJuroDia()
    {
        return $this->percentual_juro_dia;
    }

    public function setPercentualJuroDia($percentual_juro_dia): self
    {
        $this->percentual_juro_dia = $percentual_juro_dia;

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getTituloRecebers(): Collection
    {
        return $this->tituloRecebers;
    }

    public function addTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === false) {
            $this->tituloRecebers[] = $tituloReceber;
            $tituloReceber->setFranqueada($this);
        }

        return $this;
    }

    public function removeTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === true) {
            $this->tituloRecebers->removeElement($tituloReceber);
            // set the owning side to null (unless already changed)
            if ($tituloReceber->getFranqueada() === $this) {
                $tituloReceber->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DiasSubsequentes[]
     */
    public function getDiasSubsequentes(): Collection
    {
        return $this->diasSubsequentes;
    }

    public function addDiasSubsequente(DiasSubsequentes $diasSubsequente): self
    {
        if ($this->diasSubsequentes->contains($diasSubsequente) === false) {
            $this->diasSubsequentes[] = $diasSubsequente;
            $diasSubsequente->addFranqueada($this);
        }

        return $this;
    }

    public function removeDiasSubsequente(DiasSubsequentes $diasSubsequente): self
    {
        if ($this->diasSubsequentes->contains($diasSubsequente) === true) {
            $this->diasSubsequentes->removeElement($diasSubsequente);
            $diasSubsequente->removeFranqueada($this);
        }

        return $this;
    }

    public function getContaPadrao(): ?Conta
    {
        return $this->conta_padrao;
    }

    public function setContaPadrao(?Conta $conta_padrao): self
    {
        $this->conta_padrao = $conta_padrao;

        return $this;
    }

    /**
     * @return Collection|Recibo[]
     */
    public function getRecibosFranqueada(): Collection
    {
        return $this->recibosFranqueada;
    }

    public function addRecibosFranqueada(Recibo $recibosFranqueada): self
    {
        if ($this->recibosFranqueada->contains($recibosFranqueada) === false) {
            $this->recibosFranqueada[] = $recibosFranqueada;
            $recibosFranqueada->setFranqueada($this);
        }

        return $this;
    }

    public function removeRecibosFranqueada(Recibo $recibosFranqueada): self
    {
        if ($this->recibosFranqueada->contains($recibosFranqueada) === true) {
            $this->recibosFranqueada->removeElement($recibosFranqueada);
            // set the owning side to null (unless already changed)
            if ($recibosFranqueada->getFranqueada() === $this) {
                $recibosFranqueada->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getNumeroRecibo(): ?int
    {
        return $this->numero_recibo;
    }

    public function setNumeroRecibo(int $numero_recibo): self
    {
        $this->numero_recibo = $numero_recibo;

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
            $convenio->setFranqueada($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === true) {
            $this->convenios->removeElement($convenio);
            // set the owning side to null (unless already changed)
            if ($convenio->getFranqueada() === $this) {
                $convenio->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCidade(): ?Cidade
    {
        return $this->cidade;
    }

    public function setCidade(?Cidade $cidade): self
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getNumeroEndereco(): ?int
    {
        return $this->numero_endereco;
    }

    public function setNumeroEndereco(?int $numero_endereco): self
    {
        $this->numero_endereco = $numero_endereco;

        return $this;
    }

    public function getBairroEndereco(): ?string
    {
        return $this->bairro_endereco;
    }

    public function setBairroEndereco(?string $bairro_endereco): self
    {
        $this->bairro_endereco = $bairro_endereco;

        return $this;
    }

    public function getComplementoEndereco(): ?string
    {
        return $this->complemento_endereco;
    }

    public function setComplementoEndereco(?string $complemento_endereco): self
    {
        $this->complemento_endereco = $complemento_endereco;

        return $this;
    }

    public function getCepEndereco(): ?string
    {
        return $this->cep_endereco;
    }

    public function setCepEndereco(?string $cep_endereco): self
    {
        $this->cep_endereco = $cep_endereco;

        return $this;
    }

    /**
     * @return Collection|Notificacoes[]
     */
    public function getNotificacoes(): Collection
    {
        return $this->notificacoes;
    }

    public function addNotificaco(Notificacoes $notificaco): self
    {
        if ($this->notificacoes->contains($notificaco) === false) {
            $this->notificacoes[] = $notificaco;
            $notificaco->addFranqueada($this);
        }

        return $this;
    }

    /**
     * @return Collection|OperadoraCartao[]
     */
    public function getOperadoraCartaos(): Collection
    {
        return $this->operadoraCartaos;
    }

    public function addOperadoraCartao(OperadoraCartao $operadoraCartao): self
    {
        if ($this->operadoraCartaos->contains($operadoraCartao) === false) {
            $this->operadoraCartaos[] = $operadoraCartao;
            $operadoraCartao->setFranqueada($this);
        }

        return $this;
    }

    public function removeNotificaco(Notificacoes $notificaco): self
    {
        if ($this->notificacoes->contains($notificaco) === true) {
            $this->notificacoes->removeElement($notificaco);
            $notificaco->removeFranqueada($this);
        }

        return $this;
    }

    public function removeOperadoraCartao(OperadoraCartao $operadoraCartao): self
    {
        if ($this->operadoraCartaos->contains($operadoraCartao) === true) {
            $this->operadoraCartaos->removeElement($operadoraCartao);
            // set the owning side to null (unless already changed)
            if ($operadoraCartao->getFranqueada() === $this) {
                $operadoraCartao->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ModeloTemplate[]
     */
    public function getModeloTemplates(): Collection
    {
        return $this->modeloTemplates;
    }

    public function addModeloTemplate(ModeloTemplate $modeloTemplate): self
    {
        if ($this->modeloTemplates->contains($modeloTemplate) === false) {
            $this->modeloTemplates[] = $modeloTemplate;
            $modeloTemplate->setFranqueada($this);
        }

        return $this;
    }

    public function removeModeloTemplate(ModeloTemplate $modeloTemplate): self
    {
        if ($this->modeloTemplates->contains($modeloTemplate) === true) {
            $this->modeloTemplates->removeElement($modeloTemplate);
            // set the owning side to null (unless already changed)
            if ($modeloTemplate->getFranqueada() === $this) {
                $modeloTemplate->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|modeloTemplateFranqueada[]
     */
    public function getModeloTemplateFranqueadas(): Collection
    {
        return $this->modelo_template_franqueadas;
    }

    public function addModeloTemplateFranqueada(ModeloTemplateFranqueada $modeloTemplateFranqueada): self
    {
        if ($this->modelo_template_franqueadas->contains($modeloTemplateFranqueada) === false) {
            $this->modelo_template_franqueadas[] = $modeloTemplateFranqueada;
            $modeloTemplateFranqueada->setFranqueada($this);
        }

        return $this;
    }

    public function removeModeloTemplateFranqueada(ModeloTemplateFranqueada $modeloTemplateFranqueada): self
    {
        if ($this->modelo_template_franqueadas->contains($modeloTemplateFranqueada) === true) {
            $this->modelo_template_franqueadas->removeElement($modeloTemplateFranqueada);
        }

        return $this;
    }

    /**
     * @return Collection|ChecklistAtividade[]
     */
    public function getChecklistAtividades(): Collection
    {
        return $this->checklistAtividades;
    }

    public function addChecklistAtividade(ChecklistAtividade $checklistAtividade): self
    {
        if ($this->checklistAtividades->contains($checklistAtividade) === false) {
            $this->checklistAtividades[] = $checklistAtividade;
            $checklistAtividade->setFranqueada($this);
        }

        return $this;
    }

    public function removeChecklistAtividade(ChecklistAtividade $checklistAtividade): self
    {
        if ($this->checklistAtividades->contains($checklistAtividade) === true) {
            $this->checklistAtividades->removeElement($checklistAtividade);
            // set the owning side to null (unless already changed)
            if ($checklistAtividade->getFranqueada() === $this) {
                $checklistAtividade->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ChecklistAtividadeRealizada[]
     */
    public function getChecklistAtividadeRealizadas(): Collection
    {
        return $this->checklistAtividadeRealizadas;
    }

    public function addChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === false) {
            $this->checklistAtividadeRealizadas[] = $checklistAtividadeRealizada;
            $checklistAtividadeRealizada->setFranqueada($this);
        }

        return $this;
    }

    public function removeChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === true) {
            $this->checklistAtividadeRealizadas->removeElement($checklistAtividadeRealizada);
            // set the owning side to null (unless already changed)
            if ($checklistAtividadeRealizada->getFranqueada() === $this) {
                $checklistAtividadeRealizada->setFranqueada(null);
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
            $ocorrenciaAcademica->setFranqueada($this);
        }

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
            $turmaAula->setFranqueada($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === true) {
            $this->ocorrenciaAcademicas->removeElement($ocorrenciaAcademica);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademica->getFranqueada() === $this) {
                $ocorrenciaAcademica->setFranqueada(null);
            }
        }

        return $this;
    }


    public function removeTurmaAula(TurmaAula $turmaAula): self
    {
        if ($this->turmaAulas->contains($turmaAula) === true) {
            $this->turmaAulas->removeElement($turmaAula);
            // set the owning side to null (unless already changed)
            if ($turmaAula->getFranqueada() === $this) {
                $turmaAula->setFranqueada(null);
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
            $alunoDiario->setFranqueada($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getFranqueada() === $this) {
                $alunoDiario->setFranqueada(null);
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
            $alunoAvaliacao->setFranqueada($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === true) {
            $this->alunoAvaliacaos->removeElement($alunoAvaliacao);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacao->getFranqueada() === $this) {
                $alunoAvaliacao->setFranqueada(null);
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
            $alunoAvaliacaoConceitual->setFranqueada($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === true) {
            $this->alunoAvaliacaoConceituals->removeElement($alunoAvaliacaoConceitual);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitual->getFranqueada() === $this) {
                $alunoAvaliacaoConceitual->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Conta[]
     */
    public function getContas(): Collection
    {
        return $this->contas;
    }

    public function addConta(Conta $conta): self
    {
        if ($this->contas->contains($conta) === false) {
            $this->alunoAvaliacaocontaConceituals[] = $conta;
            $conta->setFranqueada($this);
        }

        return $this;
    }

    public function removeConta(Conta $conta): self
    {
        if ($this->contas->contains($conta) === true) {
            $this->contas->removeElement($conta);
            // set the owning side to null (unless already changed)
            if ($conta->getFranqueada() === $this) {
                $conta->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|AtividadeExtra[]
     */
    public function getAtividadeExtras(): Collection
    {
        return $this->atividadeExtras;
    }

    public function addAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === false) {
            $this->atividadeExtras[] = $atividadeExtra;
            $atividadeExtra->setFranqueada($this);
        }

        return $this;
    }

    public function removeAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === true) {
            $this->atividadeExtras->removeElement($atividadeExtra);
            // set the owning side to null (unless already changed)
            if ($atividadeExtra->getFranqueada() === $this) {
                $atividadeExtra->setFranqueada(null);
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
            $servico->setFranqueada($this);
        }

        return $this;
    }

    public function removeServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === true) {
            $this->servicos->removeElement($servico);
            // set the owning side to null (unless already changed)
            if ($servico->getFranqueada() === $this) {
                $servico->setFranqueada(null);
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
            $reposicaoAula->setFranqueada($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getFranqueada() === $this) {
                $reposicaoAula->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getDiasParaNegativacao(): ?int
    {
        return $this->dias_para_negativacao;
    }

    public function setDiasParaNegativacao(int $dias_para_negativacao): self
    {
        $this->dias_para_negativacao = $dias_para_negativacao;

        return $this;
    }

    public function getDiasLembreteCobranca(): ?int
    {
        return $this->dias_lembrete_cobranca;
    }

    public function setDiasLembreteCobranca(int $dias_lembrete_cobranca): self
    {
        $this->dias_lembrete_cobranca = $dias_lembrete_cobranca;

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
            $pagamentoFuncionario->setFranqueada($this);
        }

        return $this;
    }

    public function removePagamentoFuncionario(PagamentoFuncionario $pagamentoFuncionario): self
    {
        if ($this->pagamentoFuncionarios->contains($pagamentoFuncionario) === true) {
            $this->pagamentoFuncionarios->removeElement($pagamentoFuncionario);
            // set the owning side to null (unless already changed)
            if ($pagamentoFuncionario->getFranqueada() === $this) {
                $pagamentoFuncionario->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getDescontoSuperAmigosAtivo(): ?bool
    {
        return $this->desconto_super_amigos_ativo;
    }

    public function setDescontoSuperAmigosAtivo(bool $desconto_super_amigos_ativo): self
    {
        $this->desconto_super_amigos_ativo = $desconto_super_amigos_ativo;

        return $this;
    }

    public function getDescontoSuperAmigosTurbinadoAtivo(): ?bool
    {
        return $this->desconto_super_amigos_turbinado_ativo;
    }

    public function setDescontoSuperAmigosTurbinadoAtivo(bool $desconto_super_amigos_turbinado_ativo): self
    {
        $this->desconto_super_amigos_turbinado_ativo = $desconto_super_amigos_turbinado_ativo;

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
            $agendaCompromisso->setFranqueada($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getFranqueada() === $this) {
                $agendaCompromisso->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LivroBiblioteca[]
     */
    public function getLivroBibliotecas(): Collection
    {
        return $this->livroBibliotecas;
    }

    public function addLivroBiblioteca(LivroBiblioteca $livroBiblioteca): self
    {
        if ($this->livroBibliotecas->contains($livroBiblioteca) === false) {
            $this->livroBibliotecas[] = $livroBiblioteca;
            $livroBiblioteca->setFranqueada($this);
        }

        return $this;
    }

    public function removeLivroBiblioteca(LivroBiblioteca $livroBiblioteca): self
    {
        if ($this->livroBibliotecas->contains($livroBiblioteca) === true) {
            $this->livroBibliotecas->removeElement($livroBiblioteca);
            // set the owning side to null (unless already changed)
            if ($livroBiblioteca->getFranqueada() === $this) {
                $livroBiblioteca->setFranqueada(null);
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
            $emprestimoBiblioteca->setFranqueada($this);
        }

        return $this;
    }

    public function removeEmprestimoBiblioteca(EmprestimoBiblioteca $emprestimoBiblioteca): self
    {
        if ($this->emprestimoBibliotecas->contains($emprestimoBiblioteca) === true) {
            $this->emprestimoBibliotecas->removeElement($emprestimoBiblioteca);
            // set the owning side to null (unless already changed)
            if ($emprestimoBiblioteca->getFranqueada() === $this) {
                $emprestimoBiblioteca->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Calendario[]
     */
    public function getCalendarios(): Collection
    {
        return $this->calendarios;
    }

    public function addCalendario(Calendario $calendario): self
    {
        if ($this->calendarios->contains($calendario) === false) {
            $this->calendarios[] = $calendario;
            $calendario->setFranqueada($this);
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
            $bonusClass->setFranqueada($this);
        }

        return $this;
    }

    public function removeCalendario(Calendario $calendario): self
    {
        if ($this->calendarios->contains($calendario) === true) {
            $this->calendarios->removeElement($calendario);
            // set the owning side to null (unless already changed)
            if ($calendario->getFranqueada() === $this) {
                $calendario->setFranqueada(null);
            }
        }

        return $this;
    }

    public function removeBonusClass(BonusClass $bonusClass): self
    {
        if ($this->bonusClasses->contains($bonusClass) === true) {
            $this->bonusClasses->removeElement($bonusClass);
            // set the owning side to null (unless already changed)
            if ($bonusClass->getFranqueada() === $this) {
                $bonusClass->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TipoVisibilidade[]
     */
    public function getTipoVisibilidades(): Collection
    {
        return $this->tipoVisibilidades;
    }

    public function addTipoVisibilidade(TipoVisibilidade $tipoVisibilidade): self
    {
        if ($this->tipoVisibilidades->contains($tipoVisibilidade) === false) {
            $this->tipoVisibilidades[] = $tipoVisibilidade;
            $tipoVisibilidade->setFranqueada($this);
        }

        return $this;
    }

    public function removeTipoVisibilidade(TipoVisibilidade $tipoVisibilidade): self
    {
        if ($this->tipoVisibilidades->contains($tipoVisibilidade) === true) {
            $this->tipoVisibilidades->removeElement($tipoVisibilidade);
            // set the owning side to null (unless already changed)
            if ($tipoVisibilidade->getFranqueada() === $this) {
                $tipoVisibilidade->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoItem[]
     */
    public function getHistoricoItems(): Collection
    {
        return $this->historicoItems;
    }

    public function addHistoricoItem(HistoricoItem $historicoItem): self
    {
        if ($this->historicoItems->contains($historicoItem) === false) {
            $this->historicoItems[] = $historicoItem;
            $historicoItem->setFranqueada($this);
        }

        return $this;
    }

    public function removeHistoricoItem(HistoricoItem $historicoItem): self
    {
        if ($this->historicoItems->contains($historicoItem) === true) {
            $this->historicoItems->removeElement($historicoItem);
            // set the owning side to null (unless already changed)
            if ($historicoItem->getFranqueada() === $this) {
                $historicoItem->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MetaFranqueada[]
     */
    public function getMetaFranqueadas(): Collection
    {
        return $this->metaFranqueadas;
    }

    public function addMetaFranqueada(MetaFranqueada $metaFranqueada): self
    {
        if ($this->metaFranqueadas->contains($metaFranqueada) === false) {
            $this->metaFranqueadas[] = $metaFranqueada;
            $metaFranqueada->setFranqueada($this);
        }

        return $this;
    }

    /**
     * @return Collection|ConvidadoAtividadeExtra[]
     */
    public function getConvidadoAtividadeExtras(): Collection
    {
        return $this->convidadoAtividadeExtras;
    }

    public function addConvidadoAtividadeExtra(ConvidadoAtividadeExtra $convidadoAtividadeExtra): self
    {
        if ($this->convidadoAtividadeExtras->contains($convidadoAtividadeExtra) === false) {
            $this->convidadoAtividadeExtras[] = $convidadoAtividadeExtra;
            $convidadoAtividadeExtra->setFranqueada($this);
        }

        return $this;
    }

    public function removeMetaFranqueada(MetaFranqueada $metaFranqueada): self
    {
        if ($this->metaFranqueadas->contains($metaFranqueada) === true) {
            $this->metaFranqueadas->removeElement($metaFranqueada);
            // set the owning side to null (unless already changed)
            if ($metaFranqueada->getFranqueada() === $this) {
                $metaFranqueada->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MetaFranqueadaHistorico[]
     */
    public function getMetaFranqueadaHistoricos(): Collection
    {
        return $this->metaFranqueadaHistoricos;
    }

    public function addMetaFranqueadaHistorico(MetaFranqueadaHistorico $metaFranqueadaHistorico): self
    {
        if ($this->metaFranqueadaHistoricos->contains($metaFranqueadaHistorico) === false) {
            $this->metaFranqueadaHistoricos[] = $metaFranqueadaHistorico;
            $metaFranqueadaHistorico->setFranqueadaId($this);
        }

        return $this;
    }

    /**
     * @return Collection|TransferenciaBancaria[]
     */
    public function getTransferenciaBancarias(): Collection
    {
        return $this->transferenciaBancarias;
    }

    public function addTransferenciaBancaria(TransferenciaBancaria $transferenciaBancaria): self
    {
        if ($this->transferenciaBancarias->contains($transferenciaBancaria) === false) {
            $this->transferenciaBancarias[] = $transferenciaBancaria;
            $transferenciaBancaria->setFranqueada($this);
        }

        return $this;
    }

    public function removeConvidadoAtividadeExtra(ConvidadoAtividadeExtra $convidadoAtividadeExtra): self
    {
        if ($this->convidadoAtividadeExtras->contains($convidadoAtividadeExtra) === true) {
            $this->convidadoAtividadeExtras->removeElement($convidadoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($convidadoAtividadeExtra->getFranqueada() === $this) {
                $convidadoAtividadeExtra->setFranqueada(null);
            }
        }

        return $this;
    }

    public function removeMetaFranqueadaHistorico(MetaFranqueadaHistorico $metaFranqueadaHistorico): self
    {
        if ($this->metaFranqueadaHistoricos->contains($metaFranqueadaHistorico) === true) {
            $this->metaFranqueadaHistoricos->removeElement($metaFranqueadaHistorico);
            // set the owning side to null (unless already changed)
            if ($metaFranqueadaHistorico->getFranqueadaId() === $this) {
                $metaFranqueadaHistorico->setFranqueadaId(null);
            }
        }

        return $this;
    }

    public function removeTransferenciaBancaria(TransferenciaBancaria $transferenciaBancaria): self
    {
        if ($this->transferenciaBancarias->contains($transferenciaBancaria) === true) {
            $this->transferenciaBancarias->removeElement($transferenciaBancaria);
            // set the owning side to null (unless already changed)
            if ($transferenciaBancaria->getFranqueada() === $this) {
                $transferenciaBancaria->setFranqueada(null);
            }
        }

        return $this;
    }

    public function getLimiteDiasAlteracaoDocumento(): ?int
    {
        return $this->limite_dias_alteracao_documento;
    }

    public function setLimiteDiasAlteracaoDocumento(?int $limite_dias_alteracao_documento): self
    {
        $this->limite_dias_alteracao_documento = $limite_dias_alteracao_documento;

        return $this;
    }

    public function getPercentualDescontoAVista(): ?int
    {
        return $this->percentual_desconto_a_vista;
    }

    public function setPercentualDescontoAVista(?int $percentual_desconto_a_vista): self
    {
        $this->percentual_desconto_a_vista = $percentual_desconto_a_vista;

        return $this;
    }

    /**
     * @return Collection|PesquisaVisibilidade[]
     */
    public function getPesquisaVisibilidades(): Collection
    {
        return $this->pesquisaVisibilidades;
    }

    public function addPesquisaVisibilidade(PesquisaVisibilidade $pesquisaVisibilidade): self
    {
        if ($this->pesquisaVisibilidades->contains($pesquisaVisibilidade) === false) {
            $this->pesquisaVisibilidades[] = $pesquisaVisibilidade;
            $pesquisaVisibilidade->setFranqueada($this);
        }

        return $this;
    }

    public function removePesquisaVisibilidade(PesquisaVisibilidade $pesquisaVisibilidade): self
    {
        if ($this->pesquisaVisibilidades->contains($pesquisaVisibilidade) === true) {
            $this->pesquisaVisibilidades->removeElement($pesquisaVisibilidade);
            // set the owning side to null (unless already changed)
            if ($pesquisaVisibilidade->getFranqueada() === $this) {
                $pesquisaVisibilidade->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Midia[]
     */
    public function getMidias(): Collection
    {
        return $this->midias;
    }

    public function addMidia(Midia $midia): self
    {
        if ($this->midias->contains($midia) === false) {
            $this->midias[] = $midia;
            $midia->setFranqueada($this);
        }

        return $this;
    }

    public function removeMidia(Midia $midia): self
    {
        if ($this->midias->contains($midia) === true) {
            $this->midias->removeElement($midia);
            // set the owning side to null (unless already changed)
            if ($midia->getFranqueada() === $this) {
                $midia->setFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MidiaFranqueada[]
     */
    public function getMidiaFranqueadas(): Collection
    {
        return $this->midiaFranqueadas;
    }

    public function addMidiaFranqueada(MidiaFranqueada $midiaFranqueada): self
    {
        if ($this->midiaFranqueadas->contains($midiaFranqueada) === false) {
            $this->midiaFranqueadas[] = $midiaFranqueada;
            $midiaFranqueada->setFranqueada($this);
        }

        return $this;
    }

    public function removeMidiaFranqueada(MidiaFranqueada $midiaFranqueada): self
    {
        if ($this->midiaFranqueadas->contains($midiaFranqueada) === true) {
            $this->midiaFranqueadas->removeElement($midiaFranqueada);
            // set the owning side to null (unless already changed)
            if ($midiaFranqueada->getFranqueada() === $this) {
                $midiaFranqueada->setFranqueada(null);
            }
        }

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

    /**
     * @return Collection|ItemFranqueada[]
     */
    public function getItemFranqueadas(): Collection
    {
        return $this->itemFranqueadas;
    }

    public function addItemFranqueada(ItemFranqueada $itemFranqueada): self
    {
        if ($this->itemFranqueadas->contains($itemFranqueada) === false) {
            $this->itemFranqueadas[] = $itemFranqueada;
            $itemFranqueada->setFranqueada($this);
        }

        return $this;
    }

    public function removeItemFranqueada(ItemFranqueada $itemFranqueada): self
    {
        if ($this->itemFranqueadas->contains($itemFranqueada) === true) {
            $this->itemFranqueadas->removeElement($itemFranqueada);
            // set the owning side to null (unless already changed)
            if ($itemFranqueada->getFranqueada() === $this) {
                $itemFranqueada->setFranqueada(null);
            }
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
    public function setSponteId(?string $sponteId): void
    {
        $this->sponte_id = $sponteId;
    }
}
