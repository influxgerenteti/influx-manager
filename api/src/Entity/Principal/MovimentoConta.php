<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MovimentoConta
 *
 * @ORM\Table(name="movimento_conta")
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MovimentoContaRepository")
 */
class MovimentoConta
{


    public function __construct()
    {
        $this->data_movimento = new \DateTime();
        $this->data_contabil  = new \DateTime();
        $this->data_deposito  = new \DateTime();
    }

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     * Data em que foi cadastrado o movimento
     */
    private $data_movimento;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $data_contabil;

    /**
     *
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     * Data em que ocorreu o movimento
     */
    private $data_deposito;

    /**
     *
     * @var string
     *
     * @ORM\Column(type="string", length=1, nullable=false, options={"fixed"=true,"comment"="D - Débito, C - Crédito"})
     */
    private $operacao;

    /**
     *
     * @var float
     *
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=false)
     */
    private $valor_lancamento;

    /**
     *
     * @var float
     *
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=false)
     */
    private $valor_titulo;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_multa;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_juros;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_desconto;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valor_diferenca_baixa;

    /**
     *
     * @var float
     *
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=false)
     */
    private $valor_saldo_final_conta;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     *
     * @var string
     *
     * @ORM\Column(type="string", length=1, nullable=false, options={"default"="S","fixed"=true,"comment"="N - Não, S - Sim"})
     */
    private $conciliado = 'S';

    /**
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": "0"})
     */
    private $estornado = false;

    /**
     *
     * @var \App\Entity\Principal\Conta
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Conta")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta;

    /**
     *
     * @var \App\Entity\Principal\Franqueada
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     *
     * @var \App\Entity\Principal\TipoMovimentoConta
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoMovimentoConta")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo_movimento_conta;

    /**
     *
     * @var \App\Entity\Principal\TituloPagar
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloPagar")
     * @ORM\JoinColumn(nullable=true)
     */
    private $titulo_pagar;

    /**
     *
     * @var \App\Entity\Principal\TituloReceber
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloReceber")
     * @ORM\JoinColumn(nullable=true)
     */
    private $titulo_receber;

    /**
     *
     * @var \App\Entity\Principal\Usuario
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     *
     * @var \App\Entity\Principal\FormaPagamento
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento")
     * @ORM\JoinColumn(nullable=false)
     */
    private $forma_pagamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Recibo", inversedBy="movimentoContas")
     */
    private $recibo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero_documento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\MovimentoConta")
     */
    private $referencia_movimento_conta;

    /**
     * @ORM\Column(type="boolean", options={"default"="0", "comment"="Se o movimento é um estorno"})
     */
    private $movimento_estorno = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\TransacaoCartao", mappedBy="movimento_conta", cascade={"persist", "remove"})
     */
    private $transacao_cartao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Cheque", mappedBy="movimento_conta", cascade={"persist", "remove"})
     */
    private $cheque;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Boleto", mappedBy="movimento_conta", cascade={"persist", "remove"})
     */
    private $boleto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\DescontoSuperAmigo", inversedBy="movimentoContas")
     */
    private $desconto_super_amigos;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\TransferenciaBancaria", mappedBy="movimento_conta", cascade={"persist", "remove"})
     */
    private $transferencia_bancaria;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataMovimento(): ?\DateTimeInterface
    {
        return $this->data_movimento;
    }

    public function setDataMovimento(\DateTimeInterface $data_movimento): self
    {
        $this->data_movimento = $data_movimento;

        return $this;
    }

    public function getDataContabil(): ?\DateTimeInterface
    {
        return $this->data_contabil;
    }

    public function setDataContabil(\DateTimeInterface $data_contabil): self
    {
        $this->data_contabil = $data_contabil;

        return $this;
    }

    public function getDataDeposito(): ?\DateTimeInterface
    {
        return $this->data_deposito;
    }

    public function setDataDeposito(?\DateTimeInterface $data_deposito): self
    {
        $this->data_deposito = $data_deposito;

        return $this;
    }

    public function getOperacao(): ?string
    {
        return $this->operacao;
    }

    public function setOperacao(string $operacao): self
    {
        $this->operacao = $operacao;

        return $this;
    }

    public function getValorLancamento()
    {
        return $this->valor_lancamento;
    }

    public function setValorLancamento($valor_lancamento): self
    {
        $this->valor_lancamento = $valor_lancamento;

        return $this;
    }

    public function getValorTitulo()
    {
        return $this->valor_titulo;
    }

    public function setValorTitulo($valor_titulo): self
    {
        $this->valor_titulo = $valor_titulo;

        return $this;
    }

    public function getValorMulta()
    {
        return $this->valor_multa;
    }

    public function setValorMulta($valor_multa): self
    {
        $this->valor_multa = $valor_multa;

        return $this;
    }

    public function getValorJuros()
    {
        return $this->valor_juros;
    }

    public function setValorJuros($valor_juros): self
    {
        $this->valor_juros = $valor_juros;

        return $this;
    }

    public function getValorDesconto()
    {
        return $this->valor_desconto;
    }

    public function setValorDesconto($valor_desconto): self
    {
        $this->valor_desconto = $valor_desconto;

        return $this;
    }

    public function getValorDiferencaBaixa()
    {
        return $this->valor_diferenca_baixa;
    }

    public function setValorDiferencaBaixa($valor_diferenca_baixa): self
    {
        $this->valor_diferenca_baixa = $valor_diferenca_baixa;

        return $this;
    }

    public function getValorSaldoFinalConta()
    {
        return $this->valor_saldo_final_conta;
    }

    public function setValorSaldoFinalConta($valor_saldo_final_conta): self
    {
        $this->valor_saldo_final_conta = $valor_saldo_final_conta;

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

    public function getConciliado(): ?string
    {
        return $this->conciliado;
    }

    public function setConciliado(string $conciliado): self
    {
        $this->conciliado = $conciliado;

        return $this;
    }

    public function getEstornado(): ?bool
    {
        return $this->estornado;
    }

    public function setEstornado(bool $estornado): self
    {
        $this->estornado = $estornado;

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

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getTipoMovimentoConta(): ?TipoMovimentoConta
    {
        return $this->tipo_movimento_conta;
    }

    public function setTipoMovimentoConta(?TipoMovimentoConta $tipo_movimento_conta): self
    {
        $this->tipo_movimento_conta = $tipo_movimento_conta;

        return $this;
    }

    public function getTituloPagar(): ?TituloPagar
    {
        return $this->titulo_pagar;
    }

    public function setTituloPagar(?TituloPagar $titulo_pagar): self
    {
        $this->titulo_pagar = $titulo_pagar;

        return $this;
    }

    public function getTituloReceber(): ?TituloReceber
    {
        return $this->titulo_receber;
    }

    public function setTituloReceber(?TituloReceber $titulo_receber): self
    {
        $this->titulo_receber = $titulo_receber;

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

    public function getFormaPagamento(): ?FormaPagamento
    {
        return $this->forma_pagamento;
    }

    public function setFormaPagamento(?FormaPagamento $forma_pagamento): self
    {
        $this->forma_pagamento = $forma_pagamento;

        return $this;
    }

    public function getRecibo(): ?Recibo
    {
        return $this->recibo;
    }

    public function setRecibo(?Recibo $recibo): self
    {
        $this->recibo = $recibo;

        return $this;
    }

    public function getNumeroDocumento(): ?string
    {
        return $this->numero_documento;
    }

    public function setNumeroDocumento(?string $numero_documento): self
    {
        $this->numero_documento = $numero_documento;

        return $this;
    }

    public function getReferenciaMovimentoConta(): ?self
    {
        return $this->referencia_movimento_conta;
    }

    public function setReferenciaMovimentoConta(?self $referencia_movimento_conta): self
    {
        $this->referencia_movimento_conta = $referencia_movimento_conta;

        return $this;
    }

    public function getMovimentoEstorno(): ?bool
    {
        return $this->movimento_estorno;
    }

    public function setMovimentoEstorno(bool $movimento_estorno): self
    {
        $this->movimento_estorno = $movimento_estorno;

        return $this;
    }

    public function getTransacaoCartao(): ?TransacaoCartao
    {
        return $this->transacao_cartao;
    }

    public function setTransacaoCartao(?TransacaoCartao $transacao_cartao): self
    {
        $this->transacao_cartao = $transacao_cartao;

        // set (or unset) the owning side of the relation if necessary
        if ($transacao_cartao === null) {
            $newMovimento_conta = null;
        } else {
            $newMovimento_conta = $this;
        }

        if ((is_null($transacao_cartao) === false) && ($newMovimento_conta !== $transacao_cartao->getMovimentoConta())) {
            $transacao_cartao->setMovimentoConta($newMovimento_conta);
        }

        return $this;
    }

    public function getCheque(): ?Cheque
    {
        return $this->cheque;
    }

    public function setCheque(?Cheque $cheque): self
    {
        $this->cheque = $cheque;

        // set (or unset) the owning side of the relation if necessary
        if ($cheque === null) {
            $newMovimento_conta = null;
        } else {
            $newMovimento_conta = $this;
        }

        if ((is_null($cheque) === false) && ($newMovimento_conta !== $cheque->getMovimentoConta())) {
            $cheque->setMovimentoConta($newMovimento_conta);
        }

        return $this;
    }

    public function getBoleto(): ?Boleto
    {
        return $this->boleto;
    }

    public function setBoleto(?Boleto $boleto): self
    {
        $this->boleto = $boleto;

        // set (or unset) the owning side of the relation if necessary
        if ($boleto === null) {
            $newMovimento_conta = null;
        } else {
            $newMovimento_conta = $this;
        }

        if ((is_null($boleto) === false) && ($newMovimento_conta !== $boleto->getMovimentoConta())) {
            $boleto->setMovimentoConta($newMovimento_conta);
        }

        return $this;
    }

    public function getDescontoSuperAmigos(): ?DescontoSuperAmigo
    {
        return $this->desconto_super_amigos;
    }

    public function setDescontoSuperAmigos(?DescontoSuperAmigo $desconto_super_amigos): self
    {
        $this->desconto_super_amigos = $desconto_super_amigos;

        return $this;
    }

    public function getTransferenciaBancaria(): ?TransferenciaBancaria
    {
        return $this->transferencia_bancaria;
    }

    public function setTransferenciaBancaria(?TransferenciaBancaria $transferencia_bancaria): self
    {
        $this->transferencia_bancaria = $transferencia_bancaria;

        // set (or unset) the owning side of the relation if necessary
        if ($transferencia_bancaria === null) {
            $newMovimento_conta = null;
        } else {
            $newMovimento_conta = $this;
        }

        if ((is_null($transferencia_bancaria) === false) && ($newMovimento_conta !== $transferencia_bancaria->getMovimentoConta())) {
            $transferencia_bancaria->setMovimentoConta($newMovimento_conta);
        }

        return $this;
    }


}
