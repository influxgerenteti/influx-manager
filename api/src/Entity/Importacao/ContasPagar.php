<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\ContasPagarRepository")
 * @ORM\Table(name="contas_pagar")
 */
class ContasPagar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $franqueada_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Funcionario", inversedBy="contasPagars")
     * @ORM\JoinColumn(name="funcionario_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Empresa", inversedBy="contasPagars")
     * @ORM\JoinColumn(name="empresa_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $empresa;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $empresa_nome;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_parcela;

    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"comment":"CC - Cartão de Crédito, CD - Cartão de Débito, C - Cheque, CP - Cheque Pré-Datado, CB - Cobrança Bancária, CR - Crédito Recorrente, DA - Débito Automático, D - Dinheiro"})
     */
    private $tipo_recebimento;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titular;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4)
     */
    private $valor;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4, nullable=true)
     */
    private $valor_pago;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4, nullable=true)
     */
    private $valor_desconto;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4, nullable=true)
     */
    private $valor_juro;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_vencimento;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_pagamento;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4, nullable=true)
     */
    private $total_plano;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"C - Cancelada, P - Pendente, Q - Quitada"})
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numero_cheque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titular_cheque;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $banco_cheque;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $agencia_cheque;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $conta_cheque;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(int $franqueada_id): self
    {
        $this->franqueada_id = $franqueada_id;

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

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getFuncionarioNome(): ?string
    {
        return $this->funcionario_nome;
    }

    public function setFuncionarioNome(?string $funcionario_nome): self
    {
        $this->funcionario_nome = $funcionario_nome;

        return $this;
    }

    public function getEmpresaNome(): ?string
    {
        return $this->empresa_nome;
    }

    public function setEmpresaNome(?string $empresa_nome): self
    {
        $this->empresa_nome = $empresa_nome;

        return $this;
    }

    public function getNumeroParcela(): ?int
    {
        return $this->numero_parcela;
    }

    public function setNumeroParcela(?int $numero_parcela): self
    {
        $this->numero_parcela = $numero_parcela;

        return $this;
    }

    public function getTipoRecebimento(): ?string
    {
        return $this->tipo_recebimento;
    }

    public function setTipoRecebimento(?string $tipo_recebimento): self
    {
        $this->tipo_recebimento = $tipo_recebimento;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getTitular(): ?string
    {
        return $this->titular;
    }

    public function setTitular(?string $titular): self
    {
        $this->titular = $titular;

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

    public function getValorPago()
    {
        return $this->valor_pago;
    }

    public function setValorPago($valor_pago): self
    {
        $this->valor_pago = $valor_pago;

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

    public function getValorJuro()
    {
        return $this->valor_juro;
    }

    public function setValorJuro($valor_juro): self
    {
        $this->valor_juro = $valor_juro;

        return $this;
    }

    public function getDataVencimento(): ?string
    {
        return $this->data_vencimento;
    }

    public function setDataVencimento(?string $data_vencimento): self
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }

    public function getDataPagamento(): ?string
    {
        return $this->data_pagamento;
    }

    public function setDataPagamento(?string $data_pagamento): self
    {
        $this->data_pagamento = $data_pagamento;

        return $this;
    }

    public function getTotalPlano()
    {
        return $this->total_plano;
    }

    public function setTotalPlano($total_plano): self
    {
        $this->total_plano = $total_plano;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(?string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getNumeroCheque(): ?string
    {
        return $this->numero_cheque;
    }

    public function setNumeroCheque(?string $numero_cheque): self
    {
        $this->numero_cheque = $numero_cheque;

        return $this;
    }

    public function getTitularCheque(): ?string
    {
        return $this->titular_cheque;
    }

    public function setTitularCheque(?string $titular_cheque): self
    {
        $this->titular_cheque = $titular_cheque;

        return $this;
    }

    public function getBancoCheque(): ?string
    {
        return $this->banco_cheque;
    }

    public function setBancoCheque(?string $banco_cheque): self
    {
        $this->banco_cheque = $banco_cheque;

        return $this;
    }

    public function getAgenciaCheque(): ?string
    {
        return $this->agencia_cheque;
    }

    public function setAgenciaCheque(?string $agencia_cheque): self
    {
        $this->agencia_cheque = $agencia_cheque;

        return $this;
    }

    public function getContaCheque(): ?string
    {
        return $this->conta_cheque;
    }

    public function setContaCheque(?string $conta_cheque): self
    {
        $this->conta_cheque = $conta_cheque;

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


}
