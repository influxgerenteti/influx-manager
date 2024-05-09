<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ItemContaReceberRepository")
 * @ORM\Table(name="item_conta_receber")
 */
class ItemContaReceber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaReceber", inversedBy="itemsContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta_receber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item", inversedBy="itemsItemContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanoConta", inversedBy="itemsContaReceber")
     */
    private $plano_conta;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero_sequencia;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantidade;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_entrega;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="itemsEntregueContaReceber")
     */
    private $usuario_entregue;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoEstoque", mappedBy="item_conta_receber")
     */
    private $movimentoEstoques;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $percentual_desconto;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $valor_desconto;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"E - Entregue, N - Nao entregue, C - Cancelado"})
     */
    private $situacao_entrega = 'N';

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_item;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
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
    private $valor_parcela;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_parcelas;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dias_subsequentes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento", inversedBy="itensContaReceber")
     */
    private $forma_pagamento;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_vencimento;

    public function __construct()
    {
        $this->movimentoEstoques   = new ArrayCollection();
        $this->percentual_desconto = 0;
        $this->valor_desconto      = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getPlanoConta(): ?PlanoConta
    {
        return $this->plano_conta;
    }

    public function setPlanoConta(?PlanoConta $plano_conta): self
    {
        $this->plano_conta = $plano_conta;

        return $this;
    }

    public function getNumeroSequencia(): ?int
    {
        return $this->numero_sequencia;
    }

    public function setNumeroSequencia(int $numero_sequencia): self
    {
        $this->numero_sequencia = $numero_sequencia;

        return $this;
    }

    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade): self
    {
        $this->quantidade = $quantidade;

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

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getDataEntrega(): ?\DateTimeInterface
    {
        return $this->data_entrega;
    }

    public function setDataEntrega(?\DateTimeInterface $data_entrega): self
    {
        $this->data_entrega = $data_entrega;

        return $this;
    }

    public function getUsuarioEntregue(): ?Usuario
    {
        return $this->usuario_entregue;
    }

    public function setUsuarioEntregue(?Usuario $usuario_entregue): self
    {
        $this->usuario_entregue = $usuario_entregue;

        return $this;
    }

    /**
     * @return Collection|MovimentoEstoque[]
     */
    public function getMovimentoEstoques(): Collection
    {
        return $this->movimentoEstoques;
    }

    public function addMovimentoEstoque(MovimentoEstoque $movimentoEstoque): self
    {
        if ($this->movimentoEstoques->contains($movimentoEstoque) === false) {
            $this->movimentoEstoques[] = $movimentoEstoque;
            $movimentoEstoque->setItemContaReceber($this);
        }

        return $this;
    }

    public function removeMovimentoEstoque(MovimentoEstoque $movimentoEstoque): self
    {
        if ($this->movimentoEstoques->contains($movimentoEstoque) === true) {
            $this->movimentoEstoques->removeElement($movimentoEstoque);
            // set the owning side to null (unless already changed)
            if ($movimentoEstoque->getItemContaReceber() === $this) {
                $movimentoEstoque->setItemContaReceber(null);
            }
        }

        return $this;
    }

    public function getPercentualDesconto()
    {
        return $this->percentual_desconto;
    }

    public function setPercentualDesconto($percentual_desconto): self
    {
        $this->percentual_desconto = $percentual_desconto;

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

    public function getSituacaoEntrega(): ?string
    {
        return $this->situacao_entrega;
    }

    public function setSituacaoEntrega(string $situacao_entrega): self
    {
        $this->situacao_entrega = $situacao_entrega;

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

    public function getValorParcela()
    {
        return $this->valor_parcela;
    }

    public function setValorParcela($valor_parcela): self
    {
        $this->valor_parcela = $valor_parcela;

        return $this;
    }

    public function getNumeroParcelas()
    {
        return $this->numero_parcelas;
    }

    public function setNumeroParcelas($numero_parcelas): self
    {
        $this->numero_parcelas = $numero_parcelas;

        return $this;
    }

    public function getDiasSubsequentes()
    {
        return $this->dias_subsequentes;
    }

    public function setDiasSubsequentes($dias_subsequentes): self
    {
        $this->dias_subsequentes = $dias_subsequentes;

        return $this;
    }

    public function getFormaPagamento()
    {
        return $this->forma_pagamento;
    }

    public function setFormaPagamento($forma_pagamento): self
    {
        $this->forma_pagamento = $forma_pagamento;

        return $this;
    }

    public function getDataVencimento(): ?\DateTimeInterface
    {
        return $this->data_vencimento;
    }

    public function setDataVencimento(?\DateTimeInterface $data_vencimento): self
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }


}
