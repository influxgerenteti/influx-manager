<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Helper\VariaveisCompartilhadas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FormaPagamentoRepository")
 * @ORM\Table(name="forma_pagamento")
 */
class FormaPagamento
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A"})
     */
    private $situacao = 'A';

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $descricao_abreviada;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $forma_cheque = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="forma_cobranca")
     */
    private $formaCobrancaTituloReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="forma_recebimento")
     */
    private $formaRecebimentoTituloReceber;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $forma_boleto = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $forma_transferencia = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $forma_cartao = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false, "comment": "Identifica se a opção é cartão de débito, deve ser usado junto com forma_cartao=true"})
     */
    private $forma_cartao_debito = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $liquidacao_imediata = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="forma_cobranca")
     */
    private $reposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="forma_cobranca")
     */
    private $atividadeExtras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Servico", mappedBy="forma_cobranca")
     */
    private $servicos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemContaReceber", mappedBy="forma_pagamento")
     */
    private $itensContaReceber;




    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(name="franqueada_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $franqueada;

    public function __construct()
    {
      
        $this->formaCobrancaTituloReceber    = new ArrayCollection();
        $this->formaRecebimentoTituloReceber = new ArrayCollection();
        $this->reposicaoAulas    = new ArrayCollection();
        $this->atividadeExtras   = new ArrayCollection();
        $this->servicos          = new ArrayCollection();
        $this->itensContaReceber = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getDescricaoAbreviada(): ?string
    {
        return $this->descricao_abreviada;
    }

    public function setDescricaoAbreviada(string $descricao_abreviada): self
    {
        $this->descricao_abreviada = $descricao_abreviada;

        return $this;
    }

    public function getFormaCheque(): ?bool
    {
        return $this->forma_cheque;
    }

    public function setFormaCheque(bool $forma_cheque): self
    {
        $this->forma_cheque = $forma_cheque;

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getFormaCobrancaTituloReceber(): Collection
    {
        return $this->formaCobrancaTituloReceber;
    }

    public function addFormaCobrancaTituloReceber(TituloReceber $formaCobrancaTituloReceber): self
    {
        if ($this->formaCobrancaTituloReceber->contains($formaCobrancaTituloReceber) === false) {
            $this->formaCobrancaTituloReceber[] = $formaCobrancaTituloReceber;
            $formaCobrancaTituloReceber->setFormaCobranca($this);
        }

        return $this;
    }

    public function removeFormaCobrancaTituloReceber(TituloReceber $formaCobrancaTituloReceber): self
    {
        if ($this->formaCobrancaTituloReceber->contains($formaCobrancaTituloReceber) === true) {
            $this->formaCobrancaTituloReceber->removeElement($formaCobrancaTituloReceber);
            // set the owning side to null (unless already changed)
            if ($formaCobrancaTituloReceber->getFormaCobranca() === $this) {
                $formaCobrancaTituloReceber->setFormaCobranca(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getFormaRecebimentoTituloReceber(): Collection
    {
        return $this->formaRecebimentoTituloReceber;
    }

    public function addFormaRecebimentoTituloReceber(TituloReceber $formaRecebimentoTituloReceber): self
    {
        if ($this->formaRecebimentoTituloReceber->contains($formaRecebimentoTituloReceber) === false) {
            $this->formaRecebimentoTituloReceber[] = $formaRecebimentoTituloReceber;
            $formaRecebimentoTituloReceber->setFormaRecebimento($this);
        }

        return $this;
    }

    public function removeFormaRecebimentoTituloReceber(TituloReceber $formaRecebimentoTituloReceber): self
    {
        if ($this->formaRecebimentoTituloReceber->contains($formaRecebimentoTituloReceber) === true) {
            $this->formaRecebimentoTituloReceber->removeElement($formaRecebimentoTituloReceber);
            // set the owning side to null (unless already changed)
            if ($formaRecebimentoTituloReceber->getFormaRecebimento() === $this) {
                $formaRecebimentoTituloReceber->setFormaRecebimento(null);
            }
        }

        return $this;
    }

    public function getFormaBoleto(): ?bool
    {
        return $this->forma_boleto;
    }

    public function setFormaBoleto(bool $forma_boleto): self
    {
        $this->forma_boleto = $forma_boleto;

        return $this;
    }

    public function getFormaTransferencia(): ?bool
    {
        return $this->forma_transferencia;
    }

    public function setFormaTrasferencia(bool $forma_transferencia): self
    {
        $this->forma_transferencia = $forma_transferencia;

        return $this;
    }

    public function getFormaCartao(): ?bool
    {
        return $this->forma_cartao;
    }

    public function setFormaCartao(bool $forma_cartao): self
    {
        $this->forma_cartao = $forma_cartao;

        return $this;
    }

    public function getFormaCartaoDebito(): ?bool
    {
        return $this->forma_cartao_debito;
    }

    public function setFormaCartaoDebito(bool $forma_cartao_debito): self
    {
        $this->forma_cartao_debito = $forma_cartao_debito;

        return $this;
    }

    public function getLiquidacaoImediata(): ?bool
    {
        return $this->liquidacao_imediata;
    }

    public function setLiquidacaoImediata(bool $liquidacao_imediata): self
    {
        $this->liquidacao_imediata = $liquidacao_imediata;

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
            $reposicaoAula->setFormaCobranca($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getFormaCobranca() === $this) {
                $reposicaoAula->setFormaCobranca(null);
            }
        }

        return $this;
    }

    /**
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
            $atividadeExtra->setFormaCobranca($this);
        }

        return $this;
    }

    public function removeAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === true) {
            $this->atividadeExtras->removeElement($atividadeExtra);
            // set the owning side to null (unless already changed)
            if ($atividadeExtra->getFormaCobranca() === $this) {
                $atividadeExtra->setFormaCobranca(null);
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
            $servico->setFormaCobranca($this);
        }

        return $this;
    }

    public function removeServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === true) {
            $this->servicos->removeElement($servico);
            // set the owning side to null (unless already changed)
            if ($servico->getFormaCobranca() === $this) {
                $servico->setFormaCobranca(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|ItemContaReceber[]
     */
    public function getItensContaReceber(): Collection
    {
        return $this->itensContaReceber;
    }

    public function addItemContaReceber(ItemContaReceber $itemContaReceber): self
    {
        if ($this->itensContaReceber->contains($itemContaReceber) === false) {
            $this->itensContaReceber[] = $itemContaReceber;
            $itemContaReceber->setFormaPagamento($this);
        }

        return $this;
    }

    public function removeItemContaReceber(ItemContaReceber $itemContaReceber): self
    {
        if ($this->itensContaReceber->contains($itemContaReceber) === true) {
            $this->itensContaReceber->removeElement($itemContaReceber);
            // set the owning side to null (unless already changed)
            if ($itemContaReceber->getFormaPagamento() === $this) {
                $itemContaReceber->setFormaPagamento(null);
            }
        }

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

}
