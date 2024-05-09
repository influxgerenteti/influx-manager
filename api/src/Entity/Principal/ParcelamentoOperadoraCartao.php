<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ParcelamentoOperadoraCartaoRepository")
 * @ORM\Table(name="parcelamento_operadora_cartao")
 */
class ParcelamentoOperadoraCartao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\OperadoraCartao", inversedBy="parcelamentoOperadoraCartaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $operadora_cartao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanoConta", inversedBy="parcelamentoOperadoraCartaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plano_conta;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ParcelaParcelamento", mappedBy="parcelamento_operadora_cartao")
     */
    private $parcelaParcelamentos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TransacaoCartao", mappedBy="parcelamento_operadora_cartao")
     */
    private $transacoesCartao;

    public function __construct()
    {
        $this->parcelaParcelamentos = new ArrayCollection();
        $this->transacoesCartao     = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlanoConta(): ?PlanoConta
    {
        return $this->plano_conta;
    }

    public function setPlanoConta(?PlanoConta $plano_conta): self
    {
        $this->plano_conta = $plano_conta;

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

    /**
     * @return Collection|ParcelaParcelamento[]
     */
    public function getParcelaParcelamentos(): Collection
    {
        return $this->parcelaParcelamentos;
    }

    public function addParcelaParcelamento(ParcelaParcelamento $parcelaParcelamento): self
    {
        if ($this->parcelaParcelamentos->contains($parcelaParcelamento) === false) {
            $this->parcelaParcelamentos[] = $parcelaParcelamento;
            $parcelaParcelamento->setParcelamentoOperadoraCartao($this);
        }

        return $this;
    }

    public function removeParcelaParcelamento(ParcelaParcelamento $parcelaParcelamento): self
    {
        if ($this->parcelaParcelamentos->contains($parcelaParcelamento) === true) {
            $this->parcelaParcelamentos->removeElement($parcelaParcelamento);
            // set the owning side to null (unless already changed)
            if ($parcelaParcelamento->getParcelamentoOperadoraCartao() === $this) {
                $parcelaParcelamento->setParcelamentoOperadoraCartao(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TransacaoCartao[]
     */
    public function getTransacoesCartao(): Collection
    {
        return $this->transacoesCartao;
    }

    public function addTransacoesCartao(TransacaoCartao $transacoesCartao): self
    {
        if ($this->transacoesCartao->contains($transacoesCartao) === false) {
            $this->transacoesCartao[] = $transacoesCartao;
            $transacoesCartao->setParcelamentoOperadoraCartao($this);
        }

        return $this;
    }

    public function removeTransacoesCartao(TransacaoCartao $transacoesCartao): self
    {
        if ($this->transacoesCartao->contains($transacoesCartao) === true) {
            $this->transacoesCartao->removeElement($transacoesCartao);
            // set the owning side to null (unless already changed)
            if ($transacoesCartao->getParcelamentoOperadoraCartao() === $this) {
                $transacoesCartao->setParcelamentoOperadoraCartao(null);
            }
        }

        return $this;
    }


}
