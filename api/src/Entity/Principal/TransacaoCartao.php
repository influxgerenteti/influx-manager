<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TransacaoCartaoRepository")
 * @ORM\Table(name="transacao_cartao")
 */
class TransacaoCartao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="transacaoCartaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numero_lancamento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identificador;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"PEN - Pendente, CRE - Creditado, EST - Estornado, EXC - excluido"})
     */
    private $situacao = 'PEN';

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"C - crédito, D - Débito"})
     */
    private $tipo_transacao;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_liquido;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_desconto;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $taxa;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $previsao_repasse;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_pagamento;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_estorno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $titulo_receber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\MovimentoConta", inversedBy="transacao_cartao", cascade={"persist", "remove"})
     */
    private $movimento_conta;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\OperadoraCartao", inversedBy="transacoesCartao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $operadora_cartao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ParcelamentoOperadoraCartao", inversedBy="transacoesCartao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parcelamento_operadora_cartao;

    public function __construct()
    {
        $this->tituloRecebers = new ArrayCollection();
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

    public function getNumeroLancamento(): ?string
    {
        return $this->numero_lancamento;
    }

    public function setNumeroLancamento(string $numero_lancamento): self
    {
        $this->numero_lancamento = $numero_lancamento;

        return $this;
    }

    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }

    public function setIdentificador(string $identificador): self
    {
        $this->identificador = $identificador;

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

    public function getTipoTransacao(): ?string
    {
        return $this->tipo_transacao;
    }

    public function setTipoTransacao(?string $tipo_transacao): self
    {
        $this->tipo_transacao = $tipo_transacao;

        return $this;
    }

    public function getValorLiquido()
    {
        return $this->valor_liquido;
    }

    public function setValorLiquido($valor_liquido): self
    {
        $this->valor_liquido = $valor_liquido;

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

    public function getTaxa()
    {
        return $this->taxa;
    }

    public function setTaxa($taxa): self
    {
        $this->taxa = $taxa;

        return $this;
    }

    public function getPrevisaoRepasse(): ?\DateTimeInterface
    {
        return $this->previsao_repasse;
    }

    public function setPrevisaoRepasse(?\DateTimeInterface $previsao_repasse): self
    {
        $this->previsao_repasse = $previsao_repasse;

        return $this;
    }

    public function getDataPagamento(): ?\DateTimeInterface
    {
        return $this->data_pagamento;
    }

    public function setDataPagamento(?\DateTimeInterface $data_pagamento): self
    {
        $this->data_pagamento = $data_pagamento;

        return $this;
    }

    public function getDataEstorno(): ?\DateTimeInterface
    {
        return $this->data_estorno;
    }

    public function setDataEstorno(?\DateTimeInterface $data_estorno): self
    {
        $this->data_estorno = $data_estorno;

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
            $tituloReceber->setTransacaoCartao($this);
        }

        return $this;
    }

    public function removeTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === true) {
            $this->tituloRecebers->removeElement($tituloReceber);
            // set the owning side to null (unless already changed)
            if ($tituloReceber->getTransacaoCartao() === $this) {
                $tituloReceber->setTransacaoCartao(null);
            }
        }

        return $this;
    }

    public function getMovimentoConta(): ?MovimentoConta
    {
        return $this->movimento_conta;
    }

    public function setMovimentoConta(?MovimentoConta $movimento_conta): self
    {
        $this->movimento_conta = $movimento_conta;

        return $this;
    }

    public function getOperadoraCartao(): ?OperadoraCartao
    {
        return $this->operadora_cartao;
    }

    public function setOperadoraCartao(?OperadoraCartao $operadora_cartao): self
    {
        $this->operadora_cartao = $operadora_cartao;

        return $this;
    }

    public function getParcelamentoOperadoraCartao(): ?ParcelamentoOperadoraCartao
    {
        return $this->parcelamento_operadora_cartao;
    }

    public function setParcelamentoOperadoraCartao(?ParcelamentoOperadoraCartao $parcelamento_operadora_cartao): self
    {
        $this->parcelamento_operadora_cartao = $parcelamento_operadora_cartao;

        return $this;
    }


}
