<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ItemFranqueadaRepository")
 */
class ItemFranqueada
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="itemFranqueadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": "0"})
     */
    private $saldo_estoque = 0;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": "0"})
     */
    private $estoque_minimo = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_compra = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_venda = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_venda_2 = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_venda_3 = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_venda_4 = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_venda_5 = 0;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, options={"default": "0"})
     */
    private $valor_venda_6 = 0;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getSaldoEstoque()
    {
        return $this->saldo_estoque;
    }

    public function setSaldoEstoque($saldo_estoque): self
    {
        $this->saldo_estoque = $saldo_estoque;

        return $this;
    }

    public function getEstoqueMinimo()
    {
        return $this->estoque_minimo;
    }

    public function setEstoqueMinimo($estoque_minimo): self
    {
        $this->estoque_minimo = $estoque_minimo;

        return $this;
    }

    public function getValorCompra()
    {
        return $this->valor_compra;
    }

    public function setValorCompra($valor_compra): self
    {
        $this->valor_compra = $valor_compra;

        return $this;
    }

    public function getValorVenda()
    {
        return $this->valor_venda;
    }

    public function setValorVenda($valor_venda): self
    {
        $this->valor_venda = $valor_venda;

        return $this;
    }

    public function getValorVenda2()
    {
        return $this->valor_venda_2;
    }

    public function setValorVenda2($valor_venda_2): self
    {
        $this->valor_venda_2 = $valor_venda_2;

        return $this;
    }

    public function getValorVenda3()
    {
        return $this->valor_venda_3;
    }

    public function setValorVenda3($valor_venda_3): self
    {
        $this->valor_venda_3 = $valor_venda_3;

        return $this;
    }

    public function getValorVenda4()
    {
        return $this->valor_venda_4;
    }

    public function setValorVenda4($valor_venda_4): self
    {
        $this->valor_venda_4 = $valor_venda_4;

        return $this;
    }

    public function getValorVenda5()
    {
        return $this->valor_venda_5;
    }

    public function setValorVenda5($valor_venda_5): self
    {
        $this->valor_venda_5 = $valor_venda_5;

        return $this;
    }

    public function getValorVenda6()
    {
        return $this->valor_venda_6;
    }

    public function setValorVenda6($valor_venda_6): self
    {
        $this->valor_venda_6 = $valor_venda_6;

        return $this;
    }


}
