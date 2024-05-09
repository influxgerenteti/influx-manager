<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ContaRepository")
 */
class Conta
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Banco")
     * @ORM\JoinColumn(nullable=false)
     */
    private $banco;

    /**
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $numero_agencia;

    /**
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $digito_agencia;

    /**
     *
     * @ORM\Column(type="string", length=150)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $conta_corrente;

    /**
     *
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $digito_conta_corrente;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;


    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $considera_fluxo_caixa;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $empresa_no_banco;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $banco_emite_boleto;

    /**
     *
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $primeira_instrucao;

    /**
     *
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $segunda_instrucao;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_sequencia_arquivo_cobranca;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_dias_protesto;


    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao_boleto;

    /**
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $variacao_carteira;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_dias_devolucao;

    /**
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $modalidade;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_dias_floating;

    /**
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $carteira;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_contato;

    /**
     *
     * @ORM\Column(type="string", length=1)
     */
    private $situacao;

    /**
     *
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valor_saldo;

    /**
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $taxa_juro_dia;

    /**
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $taxa_multa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Boleto", mappedBy="conta")
     */
    private $boletos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="conta")
     */
    private $contaTituloRecebers;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $convenio;

    /**
     * @ORM\Column(type="integer",options={"default":0})
     */
    private $ultimo_nosso_numero;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $percentual_desconto_antecipado;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_dias_desconto_antecipado;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_dias_max_pagamento_apos_vencimento;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $texto_mora_diaria;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $texto_multa_atraso;

    public function __construct()
    {
        $this->ultimo_nosso_numero = 0;
        $this->boletos = new ArrayCollection();
        $this->contaTituloRecebers = new ArrayCollection();
    }

    public function getId()
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

    public function getBanco(): ?Banco
    {
        return $this->banco;
    }

    public function setBanco(?Banco $banco): self
    {
        $this->banco = $banco;

        return $this;
    }

    public function getNumeroAgencia(): ?string
    {
        return $this->numero_agencia;
    }

    public function setNumeroAgencia(?string $numero_agencia): self
    {
        $this->numero_agencia = $numero_agencia;

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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

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

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getConsideraFluxoCaixa(): ?bool
    {
        return $this->considera_fluxo_caixa;
    }

    public function setConsideraFluxoCaixa(bool $considera_fluxo_caixa): self
    {
        $this->considera_fluxo_caixa = $considera_fluxo_caixa;

        return $this;
    }

    public function getEmpresaNoBanco(): ?string
    {
        return $this->empresa_no_banco;
    }

    public function setEmpresaNoBanco(?string $empresa_no_banco): self
    {
        $this->empresa_no_banco = $empresa_no_banco;

        return $this;
    }

    public function getBancoEmiteBoleto(): ?bool
    {
        return $this->banco_emite_boleto;
    }

    public function setBancoEmiteBoleto(bool $banco_emite_boleto): self
    {
        $this->banco_emite_boleto = $banco_emite_boleto;

        return $this;
    }

    public function getPrimeiraInstrucao(): ?string
    {
        return $this->primeira_instrucao;
    }

    public function setPrimeiraInstrucao(?string $primeira_instrucao): self
    {
        $this->primeira_instrucao = $primeira_instrucao;

        return $this;
    }

    public function getSegundaInstrucao(): ?string
    {
        return $this->segunda_instrucao;
    }

    public function setSegundaInstrucao(?string $segunda_instrucao): self
    {
        $this->segunda_instrucao = $segunda_instrucao;

        return $this;
    }

    public function getNumeroSequenciaArquivoCobranca(): ?int
    {
        return $this->numero_sequencia_arquivo_cobranca;
    }

    public function setNumeroSequenciaArquivoCobranca(?int $numero_sequencia_arquivo_cobranca): self
    {
        $this->numero_sequencia_arquivo_cobranca = $numero_sequencia_arquivo_cobranca;

        return $this;
    }

    public function getNumeroDiasProtesto(): ?int
    {
        return $this->numero_dias_protesto;
    }

    public function setNumeroDiasProtesto(?int $numero_dias_protesto): self
    {
        $this->numero_dias_protesto = $numero_dias_protesto;

        return $this;
    }

    public function getObservacaoBoleto(): ?string
    {
        return $this->observacao_boleto;
    }

    public function setObservacaoBoleto(?string $observacao_boleto): self
    {
        $this->observacao_boleto = $observacao_boleto;

        return $this;
    }

    public function getVariacaoCarteira(): ?string
    {
        return $this->variacao_carteira;
    }

    public function setVariacaoCarteira(?string $variacao_carteira): self
    {
        $this->variacao_carteira = $variacao_carteira;

        return $this;
    }

    public function getNumeroDiasDevolucao(): ?int
    {
        return $this->numero_dias_devolucao;
    }

    public function setNumeroDiasDevolucao(?int $numero_dias_devolucao): self
    {
        $this->numero_dias_devolucao = $numero_dias_devolucao;

        return $this;
    }

    public function getModalidade(): ?string
    {
        return $this->modalidade;
    }

    public function setModalidade(?string $modalidade): self
    {
        $this->modalidade = $modalidade;

        return $this;
    }

    public function getNumeroDiasFloating(): ?int
    {
        return $this->numero_dias_floating;
    }

    public function setNumeroDiasFloating(?int $numero_dias_floating): self
    {
        $this->numero_dias_floating = $numero_dias_floating;

        return $this;
    }

    public function getCarteira(): ?string
    {
        return $this->carteira;
    }

    public function setCarteira(?string $carteira): self
    {
        $this->carteira = $carteira;

        return $this;
    }

    public function getTelefoneContato(): ?string
    {
        return $this->telefone_contato;
    }

    public function setTelefoneContato(?string $telefone_contato): self
    {
        $this->telefone_contato = $telefone_contato;

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

    public function getValorSaldo()
    {
        return $this->valor_saldo;
    }

    public function setValorSaldo($valor_saldo): self
    {
        $this->valor_saldo = $valor_saldo;

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


    public function getTaxaMulta()
    {
        return $this->taxa_multa;
    }

    public function setTaxaMulta($taxa_multa): self
    {
        $this->taxa_multa = $taxa_multa;

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
            $boleto->setConta($this);
        }

        return $this;
    }

    public function removeBoleto(Boleto $boleto): self
    {
        if ($this->boletos->contains($boleto) === true) {
            $this->boletos->removeElement($boleto);
            // set the owning side to null (unless already changed)
            if ($boleto->getConta() === $this) {
                $boleto->setConta(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getContaTituloRecebers(): Collection
    {
        return $this->contaTituloRecebers;
    }

    public function addContaTituloReceber(TituloReceber $contaTituloReceber): self
    {
        if ($this->contaTituloRecebers->contains($contaTituloReceber) === false) {
            $this->contaTituloRecebers[] = $contaTituloReceber;
            $contaTituloReceber->setConta($this);
        }

        return $this;
    }

    public function removeContaTituloReceber(TituloReceber $contaTituloReceber): self
    {
        if ($this->contaTituloRecebers->contains($contaTituloReceber) === true) {
            $this->contaTituloRecebers->removeElement($contaTituloReceber);
            // set the owning side to null (unless already changed)
            if ($contaTituloReceber->getConta() === $this) {
                $contaTituloReceber->setConta(null);
            }
        }

        return $this;
    }

    public function getConvenio(): ?string
    {
        return $this->convenio;
    }

    public function setConvenio(?string $convenio): self
    {
        $this->convenio = $convenio;

        return $this;
    }

    public function getUltimoNossoNumero(): ?int
    {
        return $this->ultimo_nosso_numero;
    }

    public function setUltimoNossoNumero(int $ultimo_nosso_numero): self
    {
        $this->ultimo_nosso_numero = $ultimo_nosso_numero;

        return $this;
    }

    public function getPercentualDescontoAntecipado()
    {
        return $this->percentual_desconto_antecipado;
    }

    public function setPercentualDescontoAntecipado($percentual_desconto_antecipado): self
    {
        $this->percentual_desconto_antecipado = $percentual_desconto_antecipado;

        return $this;
    }

    public function getNumeroDiasDescontoAntecipado(): ?int
    {
        return $this->numero_dias_desconto_antecipado;
    }

    public function setNumeroDiasDescontoAntecipado(?int $numero_dias_desconto_antecipado): self
    {
        $this->numero_dias_desconto_antecipado = $numero_dias_desconto_antecipado;

        return $this;
    }

    public function getNumeroDiasMaxPagamentoAposVencimento(): ?int
    {
        return $this->numero_dias_max_pagamento_apos_vencimento;
    }

    public function setNumeroDiasMaxPagamentoAposVencimento(?int $numero_dias_max_pagamento_apos_vencimento): self
    {
        $this->numero_dias_max_pagamento_apos_vencimento = $numero_dias_max_pagamento_apos_vencimento;

        return $this;
    }

    public function getTextoMoraDiaria(): ?string
    {
        return $this->texto_mora_diaria;
    }

    public function setTextoMoraDiaria(?string $texto_mora_diaria): self
    {
        $this->texto_mora_diaria = $texto_mora_diaria;

        return $this;
    }

    public function getTextoMultaAtraso(): ?string
    {
        return $this->texto_multa_atraso;
    }

    public function setTextoMultaAtraso(?string $texto_multa_atraso): self
    {
        $this->texto_multa_atraso = $texto_multa_atraso;

        return $this;
    }


}
