<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TituloReceberRepository")
 * @ORM\Table(name="titulo_receber")
 */
class TituloReceber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="tituloRecebers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaReceber", inversedBy="tituloRecebers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta_receber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa", inversedBy="tituloRecebers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sacado_pessoa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoTituloRecebers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Conta", inversedBy="contaTituloRecebers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento", inversedBy="formaCobrancaTituloReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $forma_cobranca;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento", inversedBy="formaRecebimentoTituloReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $forma_recebimento;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_vencimento;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_prorrogacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_emissao;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     * *O 'valor original' é o valor após aplicar o desconto, o valor do montante original mesmo é o 'valor_parcela_sem_desconto'
     */
    private $valor_original;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true, options={"comment":"despesas da transação, exemplo custo do Cartão de débito/crédito"})
     */
    private $valor_despesas;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $taxa_multa;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $taxa_juro_dia;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true, options={"comment":"deve ser preenchido com o mesmo valor do valor título - vem da requisição"})
     */
    private $valor_saldo_devedor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"PEN - Pendente, LIQ - Liquidado, BAI - Baixado, SUB - Substituido"})
     */
    private $situacao = "PEN";

    /**
     * @ORM\Column(type="integer")
     */
    private $numero_parcela_documento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoConta", mappedBy="titulo_receber")
     */
    private $movimento_conta;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Boleto", mappedBy="titulo_receber")
     */
    private $boletos;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cheque", mappedBy="titulo_receber")
     */
    private $cheques;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TransacaoCartao", mappedBy="titulo_receber")
     */
    private $transacoes_cartao;

    /**
     * @ORM\Column(type="boolean")
     */
    private $negativado = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $lembrete_envio = false;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $renegociado = false;

    /**
     * @ORM\ManyToMany(targetEntity="\App\Entity\Principal\Renegociacao", inversedBy="titulos_receber")
     */
    private $renegociacoes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TransferenciaBancaria", mappedBy="titulo_receber", orphanRemoval=true)
     */
    private $transferencias_bancarias;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_item;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     * *Esse é o valor do montante original
     */
    private $valor_parcela_sem_desconto;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $desconto_antecipacao;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_desconto_super_amigo;

     /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_desconto_manual;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motivo_desconto_manual;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $usuario_desconto_manual;

    public function __construct()
    {
        $this->movimento_conta   = new ArrayCollection();
        $this->boletos           = new ArrayCollection();
        $this->cheques           = new ArrayCollection();
        $this->transacoes_cartao = new ArrayCollection();
        $this->renegociacoes     = new ArrayCollection();
        $this->transferencias_bancarias = new ArrayCollection();
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

    public function getContaReceber(): ?ContaReceber
    {
        return $this->conta_receber;
    }

    public function setContaReceber(?ContaReceber $conta_receber): self
    {
        $this->conta_receber = $conta_receber;

        return $this;
    }

    public function getSacadoPessoa(): ?Pessoa
    {
        return $this->sacado_pessoa;
    }

    public function setSacadoPessoa(?Pessoa $sacado_pessoa): self
    {
        $this->sacado_pessoa = $sacado_pessoa;

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

    public function getConta(): ?Conta
    {
        return $this->conta;
    }

    public function setConta(?Conta $conta): self
    {
        $this->conta = $conta;

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

    public function getFormaRecebimento(): ?FormaPagamento
    {
        return $this->forma_recebimento;
    }

    public function setFormaRecebimento(?FormaPagamento $forma_recebimento): self
    {
        $this->forma_recebimento = $forma_recebimento;

        return $this;
    }

    public function getDataVencimento(): ?\DateTimeInterface
    {
        return $this->data_vencimento;
    }

    public function setDataVencimento(\DateTimeInterface $data_vencimento): self
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }

    public function getDataProrrogacao(): ?\DateTimeInterface
    {
        return $this->data_prorrogacao;
    }

    public function setDataProrrogacao(\DateTimeInterface $data_prorrogacao): self
    {
        $this->data_prorrogacao = $data_prorrogacao;

        return $this;
    }

    public function getDataEmissao(): ?\DateTimeInterface
    {
        return $this->data_emissao;
    }

    public function setDataEmissao(?\DateTimeInterface $data_emissao): self
    {
        $this->data_emissao = $data_emissao;

        return $this;
    }

    public function getValorOriginal()
    {
        return $this->valor_original;
    }

    public function setValorOriginal($valor_original): self
    {
        $this->valor_original = $valor_original;

        return $this;
    }

    public function getValorDespesas()
    {
        return $this->valor_despesas;
    }

    public function setValorDespesas($valor_despesas): self
    {
        $this->valor_despesas = $valor_despesas;

        return $this;
    }

    public function getTaxaMulta()
    {
        return $this->taxa_multa;
    }

    public function setTaxaMulta($taxa_multa): self
    {
        $this->taxa_multa = $taxa_multa;

        return $this;
    }

    public function getTaxaJuroDia()
    {
        return $this->taxa_juro_dia;
    }

    public function setTaxaJuroDia($taxa_juro_dia): self
    {
        $this->taxa_juro_dia = $taxa_juro_dia;

        return $this;
    }

    public function getValorSaldoDevedor()
    {
        return $this->valor_saldo_devedor;
    }

    public function setValorSaldoDevedor($valor_saldo_devedor): self
    {
        $this->valor_saldo_devedor = $valor_saldo_devedor;

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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getNumeroParcelaDocumento(): ?int
    {
        return $this->numero_parcela_documento;
    }

    public function setNumeroParcelaDocumento(int $numero_parcela_documento): self
    {
        $this->numero_parcela_documento = $numero_parcela_documento;

        return $this;
    }

    /**
     *
     * @return Collection|MovimentoConta[]
     */
    public function getMovimentoConta() : Collection
    {
        return $this->movimento_conta;
    }

    public function addMovimentoConta(MovimentoConta $movimentoConta) : self
    {
        if ($this->movimento_conta->contains($movimentoConta) === false) {
            $this->movimento_conta[] = $movimentoConta;
            $movimentoConta->setTituloReceber($this);
        }

        return $this;
    }

    public function removeMovimentoConta(MovimentoConta $movimentoConta) : self
    {
        if ($this->movimento_conta->contains($movimentoConta) === true) {
            $this->movimento_conta->removeElement($movimentoConta);
            // set the owning side to null (unless already changed)
            if ($movimentoConta->getTituloReceber() === $this) {
                $movimentoConta->setTituloReceber(null);
            }
        }

        return $this;
    }

    /**
     *
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
            $boleto->setTituloReceber($this);
        }

        return $this;
    }

    public function removeBoleto(Boleto $boleto): self
    {
        if ($this->boletos->contains($boleto) === true) {
            $this->boletos->removeElement($boleto);
            // set the owning side to null (unless already changed)
            if ($boleto->getTituloReceber() === $this) {
                $boleto->setTituloReceber(null);
            }
        }

        return $this;
    }

    /**
     *
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
            $cheque->setTituloReceber($this);
        }

        return $this;
    }

    public function removeCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === true) {
            $this->cheques->removeElement($cheque);
            // set the owning side to null (unless already changed)
            if ($cheque->getTituloReceber() === $this) {
                $cheque->setTituloReceber(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|TransacaoCartao[]
     */
    public function getTransacoesCartao(): Collection
    {
        return $this->transacoes_cartao;
    }

    public function addTransacaoCartao(TransacaoCartao $transacao_cartao): self
    {
        if ($this->transacoes_cartao->contains($transacao_cartao) === false) {
            $this->transacoes_cartao[] = $transacao_cartao;
            $transacao_cartao->setTituloReceber($this);
        }

        return $this;
    }

    public function removeTransacaoCartao(TransacaoCartao $transacao_cartao): self
    {
        if ($this->transacoes_cartao->contains($transacao_cartao) === true) {
            $this->transacoes_cartao->removeElement($transacao_cartao);
            // set the owning side to null (unless already changed)
            if ($transacao_cartao->getTituloReceber() === $this) {
                $transacao_cartao->setTituloReceber(null);
            }
        }

        return $this;
    }

    public function getNegativado(): ?bool
    {
        return $this->negativado;
    }

    public function setNegativado(bool $negativado): self
    {
        $this->negativado = $negativado;

        return $this;
    }

    public function getLembreteEnvio(): ?bool
    {
        return $this->lembrete_envio;
    }

    public function setLembreteEnvio(bool $lembrete_envio): self
    {
        $this->lembrete_envio = $lembrete_envio;

        return $this;
    }

    public function getRenegociado(): ?bool
    {
        return $this->renegociado;
    }

    public function setRenegociado(bool $renegociado): self
    {
        $this->renegociado = $renegociado;

        return $this;
    }

    /**
     * @return Collection|Renegociacao[]
     */
    public function getRenegociacoes(): Collection
    {
        return $this->renegociacoes;
    }

    public function addRenegociacao(Renegociacao $renegociacao): self
    {
        if ($this->renegociacoes->contains($renegociacao) === false) {
            $this->renegociacoes[] = $renegociacao;
        }

        return $this;
    }

    public function removeRenegociacao(Renegociacao $renegociacao): self
    {
        if ($this->renegociacoes->contains($renegociacao) === true) {
            $this->renegociacoes->removeElement($renegociacao);
        }

        return $this;
    }

    /**
     * @return Collection|TransferenciaBancaria[]
     */
    public function getTransferenciasBancaria(): Collection
    {
        return $this->transferencias_bancarias;
    }

    public function addTransferenciaBancaria(TransferenciaBancaria $transferenciaBancaria): self
    {
        if ($this->transferencias_bancarias->contains($transferenciaBancaria) === false) {
            $this->transferencias_bancarias[] = $transferenciaBancaria;
            $transferenciaBancaria->setTituloReceber($this);
        }

        return $this;
    }

    public function removeTransferenciaBancaria(TransferenciaBancaria $transferenciaBancaria): self
    {
        if ($this->transferencias_bancarias->contains($transferenciaBancaria) === true) {
            $this->transferencias_bancarias->removeElement($transferenciaBancaria);
            // set the owning side to null (unless already changed)
            if ($transferenciaBancaria->getTituloReceber() === $this) {
                $transferenciaBancaria->setTituloReceber(null);
            }
        }

        return $this;
    }

    public function getValorItem()
    {
        return $this->valor_item;
    }

    public function setValorItem($valor_item): self
    {
        $this->valor_item = $valor_item;

        return $this;
    }

    public function getValorParcelaSemDesconto()
    {
        return $this->valor_parcela_sem_desconto;
    }

    public function setValorParcelaSemDesconto($valor_parcela_sem_desconto): self
    {
        $this->valor_parcela_sem_desconto = $valor_parcela_sem_desconto;

        return $this;
    }

    public function getDescontoAntecipacao()
    {
        return $this->desconto_antecipacao;
    }

    public function setDescontoAntecipacao($desconto_antecipacao): self
    {
        $this->desconto_antecipacao = $desconto_antecipacao;

        return $this;
    }

    public function getValorDescontoSuperAmigo()
    {
        return $this->valor_desconto_super_amigo;
    }

    public function setValorDescontoSuperAmigo($valor_desconto_super_amigo): self
    {
        $this->valor_desconto_super_amigo = $valor_desconto_super_amigo;

        return $this;
    }



    /**
     * Get the value of valor_desconto_manual
     */ 
    public function getValor_desconto_manual()
    {
        return $this->valor_desconto_manual;
    }

    /**
     * Set the value of valor_desconto_manual
     *
     * @return  self
     */ 
    public function setValor_desconto_manual($valor_desconto_manual)
    {
        $this->valor_desconto_manual = $valor_desconto_manual;

        return $this;
    }

    /**
     * Get the value of motivo_desconto_manual
     */ 
    public function getMotivo_desconto_manual()
    {
        return $this->motivo_desconto_manual;
    }

    /**
     * Set the value of motivo_desconto_manual
     *
     * @return  self
     */ 
    public function setMotivo_desconto_manual($motivo_desconto_manual)
    {
        $this->motivo_desconto_manual = $motivo_desconto_manual;

        return $this;
    }

    /**
     * Get the value of usuario_desconto_manual
     */ 
    public function getUsuario_desconto_manual()
    {
        return $this->usuario_desconto_manual;
    }

    /**
     * Set the value of usuario_desconto_manual
     *
     * @return  self
     */ 
    public function setUsuario_desconto_manual($usuario_desconto_manual)
    {
        $this->usuario_desconto_manual = $usuario_desconto_manual;

        return $this;
    }
}
