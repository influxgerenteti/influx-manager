<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PagamentoFuncionarioRepository")
 */
class PagamentoFuncionario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="pagamentoFuncionarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="pagamentoFuncionarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_pagamento = null;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"(PEN)dente, (P)agamento (E)m (A)ndamento, (PAG)a, (CAN)celada"})
     */
    private $situacao = 'PEN';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaPagar", inversedBy="pagamentoFuncionarios")
     */
    private $conta_pagar;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $valor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\CabecalhoPagamento", mappedBy="pagamento_funcionario")
     */
    private $cabecalhoPagamentos;

    public function __construct()
    {
        $this->data_criacao        = new \DateTime();
        $this->cabecalhoPagamentos = new ArrayCollection();
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

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getContaPagar(): ?ContaPagar
    {
        return $this->conta_pagar;
    }

    public function setContaPagar(?ContaPagar $conta_pagar): self
    {
        $this->conta_pagar = $conta_pagar;

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

    /**
     * @return Collection|CabecalhoPagamento[]
     */
    public function getCabecalhoPagamentos(): Collection
    {
        return $this->cabecalhoPagamentos;
    }

    public function addCabecalhoPagamento(CabecalhoPagamento $cabecalhoPagamento): self
    {
        if ($this->cabecalhoPagamentos->contains($cabecalhoPagamento) === false) {
            $this->cabecalhoPagamentos[] = $cabecalhoPagamento;
            $cabecalhoPagamento->setPagamentoFuncionario($this);
        }

        return $this;
    }

    public function removeCabecalhoPagamento(CabecalhoPagamento $cabecalhoPagamento): self
    {
        if ($this->cabecalhoPagamentos->contains($cabecalhoPagamento) === true) {
            $this->cabecalhoPagamentos->removeElement($cabecalhoPagamento);
            // set the owning side to null (unless already changed)
            if ($cabecalhoPagamento->getPagamentoFuncionario() === $this) {
                $cabecalhoPagamento->setPagamentoFuncionario(null);
            }
        }

        return $this;
    }


}
