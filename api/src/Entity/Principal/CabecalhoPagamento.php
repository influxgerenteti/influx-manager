<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CabecalhoPagamentoRepository")
 */
class CabecalhoPagamento
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PagamentoFuncionario", inversedBy="cabecalhoPagamentos")
     */
    private $pagamento_funcionario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=3, options={"comment": "(TUR)mas, (PER)sonal, (ATI)vidade extra"})
     */
    private $tipo_pagamento;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $quantidade_registros = 0.0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_hora = 0.0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_extra = 0.0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_bonus = 0.0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $total_valor_hora = 0.0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $total_valor_bonus = 0.0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_total = 0.0;

    /**
     * @ORM\Column(type="string", length=3, options={"default": "PEN", "comment": "(PEN)dente, (P)agamento (E)m (A)ndamento, (PAG)a, (CAN)celada"})
     */
    private $situacao = 'PEN';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoTurmaAula", mappedBy="cabecalho_pagamento", orphanRemoval=true)
     */
    private $pagamentoTurmaAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoAlunoDiarioPersonal", mappedBy="cabecalho_pagamento", orphanRemoval=true)
     */
    private $pagamentoAlunoDiarioPersonals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoAtividadeExtra", mappedBy="cabecalho_pagamento", orphanRemoval=true)
     */
    private $pagamentoAtividadeExtras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoReposicaoAula", mappedBy="cabecalho_pagamento", orphanRemoval=true)
     */
    private $pagamentoReposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoBonusClass", mappedBy="cabecalho_pagamento", orphanRemoval=true)
     */
    private $pagamentoBonusClasses;

    public function __construct()
    {
        $this->pagamentoTurmaAulas           = new ArrayCollection();
        $this->pagamentoAlunoDiarioPersonals = new ArrayCollection();
        $this->pagamentoAtividadeExtras      = new ArrayCollection();
        $this->pagamentoReposicaoAulas       = new ArrayCollection();
        $this->pagamentoBonusClasses         = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPagamentoFuncionario(): ?PagamentoFuncionario
    {
        return $this->pagamento_funcionario;
    }

    public function setPagamentoFuncionario(?PagamentoFuncionario $pagamento_funcionario): self
    {
        $this->pagamento_funcionario = $pagamento_funcionario;

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

    public function getTipoPagamento(): ?string
    {
        return $this->tipo_pagamento;
    }

    public function setTipoPagamento(string $tipo_pagamento): self
    {
        $this->tipo_pagamento = $tipo_pagamento;

        return $this;
    }

    public function getQuantidadeRegistros(): ?float
    {
        return $this->quantidade_registros;
    }

    public function setQuantidadeRegistros(float $quantidade_registros): self
    {
        $this->quantidade_registros = $quantidade_registros;

        return $this;
    }

    public function getValorHora()
    {
        return $this->valor_hora;
    }

    public function setValorHora($valor_hora): self
    {
        $this->valor_hora = $valor_hora;

        return $this;
    }

    public function getValorExtra()
    {
        return $this->valor_extra;
    }

    public function setValorExtra($valor_extra): self
    {
        $this->valor_extra = $valor_extra;

        return $this;
    }

    public function getValorBonus()
    {
        return $this->valor_bonus;
    }

    public function setValorBonus($valor_bonus): self
    {
        $this->valor_bonus = $valor_bonus;

        return $this;
    }

    public function getTotalValorHora()
    {
        return $this->total_valor_hora;
    }

    public function setTotalValorHora($total_valor_hora): self
    {
        $this->total_valor_hora = $total_valor_hora;

        return $this;
    }

    public function getTotalValorBonus()
    {
        return $this->total_valor_bonus;
    }

    public function setTotalValorBonus($total_valor_bonus): self
    {
        $this->total_valor_bonus = $total_valor_bonus;

        return $this;
    }

    public function getValorTotal()
    {
        return $this->valor_total;
    }

    public function setValorTotal($valor_total): self
    {
        $this->valor_total = $valor_total;

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
     * @return Collection|PagamentoTurmaAula[]
     */
    public function getPagamentoTurmaAulas(): Collection
    {
        return $this->pagamentoTurmaAulas;
    }

    public function addPagamentoTurmaAula(PagamentoTurmaAula $pagamentoTurmaAula): self
    {
        if ($this->pagamentoTurmaAulas->contains($pagamentoTurmaAula) === false) {
            $this->pagamentoTurmaAulas[] = $pagamentoTurmaAula;
            $pagamentoTurmaAula->setCabecalhoPagamento($this);
        }

        return $this;
    }

    public function removePagamentoTurmaAula(PagamentoTurmaAula $pagamentoTurmaAula): self
    {
        if ($this->pagamentoTurmaAulas->contains($pagamentoTurmaAula) === true) {
            $this->pagamentoTurmaAulas->removeElement($pagamentoTurmaAula);
            // set the owning side to null (unless already changed)
            if ($pagamentoTurmaAula->getCabecalhoPagamento() === $this) {
                $pagamentoTurmaAula->setCabecalhoPagamento(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoAlunoDiarioPersonal[]
     */
    public function getPagamentoAlunoDiarioPersonals(): Collection
    {
        return $this->pagamentoAlunoDiarioPersonals;
    }

    public function addPagamentoAlunoDiarioPersonal(PagamentoAlunoDiarioPersonal $pagamentoAlunoDiarioPersonal): self
    {
        if ($this->pagamentoAlunoDiarioPersonals->contains($pagamentoAlunoDiarioPersonal) === false) {
            $this->pagamentoAlunoDiarioPersonals[] = $pagamentoAlunoDiarioPersonal;
            $pagamentoAlunoDiarioPersonal->setCabecalhoPagamento($this);
        }

        return $this;
    }

    public function removePagamentoAlunoDiarioPersonal(PagamentoAlunoDiarioPersonal $pagamentoAlunoDiarioPersonal): self
    {
        if ($this->pagamentoAlunoDiarioPersonals->contains($pagamentoAlunoDiarioPersonal) === true) {
            $this->pagamentoAlunoDiarioPersonals->removeElement($pagamentoAlunoDiarioPersonal);
            // set the owning side to null (unless already changed)
            if ($pagamentoAlunoDiarioPersonal->getCabecalhoPagamento() === $this) {
                $pagamentoAlunoDiarioPersonal->setCabecalhoPagamento(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoAtividadeExtra[]
     */
    public function getPagamentoAtividadeExtras(): Collection
    {
        return $this->pagamentoAtividadeExtras;
    }

    public function addPagamentoAtividadeExtra(PagamentoAtividadeExtra $pagamentoAtividadeExtra): self
    {
        if ($this->pagamentoAtividadeExtras->contains($pagamentoAtividadeExtra) === false) {
            $this->pagamentoAtividadeExtras[] = $pagamentoAtividadeExtra;
            $pagamentoAtividadeExtra->setCabecalhoPagamento($this);
        }

        return $this;
    }

    public function removePagamentoAtividadeExtra(PagamentoAtividadeExtra $pagamentoAtividadeExtra): self
    {
        if ($this->pagamentoAtividadeExtras->contains($pagamentoAtividadeExtra) === true) {
            $this->pagamentoAtividadeExtras->removeElement($pagamentoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($pagamentoAtividadeExtra->getCabecalhoPagamento() === $this) {
                $pagamentoAtividadeExtra->setCabecalhoPagamento(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoReposicaoAula[]
     */
    public function getpagamentoReposicaoAulas(): Collection
    {
        return $this->pagamentoReposicaoAulas;
    }

    public function addPagamentoReposicaoAula(PagamentoReposicaoAula $PagamentoReposicaoAula): self
    {
        if ($this->pagamentoReposicaoAulas->contains($PagamentoReposicaoAula) === false) {
            $this->pagamentoReposicaoAulas[] = $PagamentoReposicaoAula;
            $PagamentoReposicaoAula->setCabecalhoPagamento($this);
        }

        return $this;
    }

    public function removePagamentoReposicaoAula(PagamentoReposicaoAula $PagamentoReposicaoAula): self
    {
        if ($this->pagamentoReposicaoAulas->contains($PagamentoReposicaoAula) === true) {
            $this->pagamentoReposicaoAulas->removeElement($PagamentoReposicaoAula);
            // set the owning side to null (unless already changed)
            if ($PagamentoReposicaoAula->getCabecalhoPagamento() === $this) {
                $PagamentoReposicaoAula->setCabecalhoPagamento(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoBonusClass[]
     */
    public function getPagamentoBonusClasses(): Collection
    {
        return $this->pagamentoBonusClasses;
    }

    public function addPagamentoBonusClass(PagamentoBonusClass $pagamentoBonusClass): self
    {
        if ($this->pagamentoBonusClasses->contains($pagamentoBonusClass) === false) {
            $this->pagamentoBonusClasses[] = $pagamentoBonusClass;
            $pagamentoBonusClass->setCabecalhoPagamento($this);
        }

        return $this;
    }

    public function removePagamentoBonusClass(PagamentoBonusClass $pagamentoBonusClass): self
    {
        if ($this->pagamentoBonusClasses->contains($pagamentoBonusClass) === true) {
            $this->pagamentoBonusClasses->removeElement($pagamentoBonusClass);
            // set the owning side to null (unless already changed)
            if ($pagamentoBonusClass->getCabecalhoPagamento() === $this) {
                $pagamentoBonusClass->setCabecalhoPagamento(null);
            }
        }

        return $this;
    }


}
