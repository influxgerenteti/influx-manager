<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CondicaoPagamentoRepository")
 * @ORM\Table(name="condicao_pagamento")
 */
class CondicaoPagamento
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
     * @ORM\Column(type="string", length=30)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $quantidade_parcelas;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\CondicaoPagamentoParcela", mappedBy="condicao_pagamento", orphanRemoval=true)
     */
    private $condicaoPagamentoParcelas;

    public function __construct()
    {
        $this->condicaoPagamentoParcelas = new ArrayCollection();
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

    public function getQuantidadeParcelas(): ?int
    {
        return $this->quantidade_parcelas;
    }

    public function setQuantidadeParcelas(int $quantidade_parcelas): self
    {
        $this->quantidade_parcelas = $quantidade_parcelas;

        return $this;
    }

    /**
     *
     * @return Collection|CondicaoPagamentoParcela[]
     */
    public function getCondicaoPagamentoParcelas(): Collection
    {
        return $this->condicaoPagamentoParcelas;
    }

    public function addCondicaoPagamentoParcela(CondicaoPagamentoParcela $condicaoPagamentoParcela): self
    {
        if ($this->condicaoPagamentoParcelas->contains($condicaoPagamentoParcela) === false) {
            $this->condicaoPagamentoParcelas[] = $condicaoPagamentoParcela;
            $condicaoPagamentoParcela->setCondicaoPagamento($this);
        }

        return $this;
    }

    public function removeCondicaoPagamentoParcela(CondicaoPagamentoParcela $condicaoPagamentoParcela): self
    {
        if ($this->condicaoPagamentoParcelas->contains($condicaoPagamentoParcela) === true) {
            $this->condicaoPagamentoParcelas->removeElement($condicaoPagamentoParcela);
            // set the owning side to null (unless already changed)
            if ($condicaoPagamentoParcela->getCondicaoPagamento() === $this) {
                $condicaoPagamentoParcela->setCondicaoPagamento(null);
            }
        }

        return $this;
    }


}
