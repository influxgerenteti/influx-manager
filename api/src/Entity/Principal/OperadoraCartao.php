<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\OperadoraCartaoRepository")
 * @ORM\Table(name="operadora_cartao")
 */
class OperadoraCartao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="operadoraCartaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"C - Crédito, D - Débito"})
     */
    private $tipo_operacao = 'C';

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ParcelamentoOperadoraCartao", mappedBy="operadora_cartao")
     */
    private $parcelamentoOperadoraCartaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TransacaoCartao", mappedBy="operadora_cartao")
     */
    private $transacoesCartao;

    public function __construct()
    {
        $this->parcelamentoOperadoraCartaos = new ArrayCollection();
        $this->transacoesCartao = new ArrayCollection();
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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getTipoOperacao(): ?string
    {
        return $this->tipo_operacao;
    }

    public function setTipoOperacao(string $tipo_operacao): self
    {
        $this->tipo_operacao = $tipo_operacao;

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
     * @return Collection|ParcelamentoOperadoraCartao[]
     */
    public function getParcelamentoOperadoraCartaos(): Collection
    {
        return $this->parcelamentoOperadoraCartaos;
    }

    public function addParcelamentoOperadoraCartao(ParcelamentoOperadoraCartao $parcelamentoOperadoraCartao): self
    {
        if ($this->parcelamentoOperadoraCartaos->contains($parcelamentoOperadoraCartao) === false) {
            $this->parcelamentoOperadoraCartaos[] = $parcelamentoOperadoraCartao;
            $parcelamentoOperadoraCartao->setOperadoraCartao($this);
        }

        return $this;
    }

    public function removeParcelamentoOperadoraCartao(ParcelamentoOperadoraCartao $parcelamentoOperadoraCartao): self
    {
        if ($this->parcelamentoOperadoraCartaos->contains($parcelamentoOperadoraCartao) === true) {
            $this->parcelamentoOperadoraCartaos->removeElement($parcelamentoOperadoraCartao);
            // set the owning side to null (unless already changed)
            if ($parcelamentoOperadoraCartao->getOperadoraCartao() === $this) {
                $parcelamentoOperadoraCartao->setOperadoraCartao(null);
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
            $transacoesCartao->setBandeira($this);
        }

        return $this;
    }

    public function removeTransacoesCartao(TransacaoCartao $transacoesCartao): self
    {
        if ($this->transacoesCartao->contains($transacoesCartao) === true) {
            $this->transacoesCartao->removeElement($transacoesCartao);
            // set the owning side to null (unless already changed)
            if ($transacoesCartao->getBandeira() === $this) {
                $transacoesCartao->setBandeira(null);
            }
        }

        return $this;
    }


}
