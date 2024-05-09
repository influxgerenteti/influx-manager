<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MovimentoEstoqueRepository")
 * @ORM\Table(name="movimento_estoque")
 */
class MovimentoEstoque
{


    public function __construct()
    {
        $this->data_movimento = new \DateTime();
    }

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoMovimentoEstoque")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo_movimento_estoque;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_movimento;

    /**
     *
     * @ORM\Column(type="decimal", precision=15, scale=6, nullable=true)
     */
    private $quantidade_item;

    /**
     *
     * @ORM\Column(type="decimal", precision=15, scale=6, nullable=true)
     */
    private $quantidade_saldo_final;

    /**
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_movimento;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ItemContaReceber", inversedBy="movimentoEstoques")
     */
    private $item_conta_receber;

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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getTipoMovimentoEstoque(): ?TipoMovimentoEstoque
    {
        return $this->tipo_movimento_estoque;
    }

    public function setTipoMovimentoEstoque(?TipoMovimentoEstoque $tipo_movimento_estoque): self
    {
        $this->tipo_movimento_estoque = $tipo_movimento_estoque;

        return $this;
    }

    public function getDataMovimento(): ?\DateTimeInterface
    {
        return $this->data_movimento;
    }

    public function setDataMovimento(\DateTimeInterface $data_movimento): self
    {
        $this->data_movimento = $data_movimento;

        return $this;
    }

    public function getQuantidadeItem()
    {
        return $this->quantidade_item;
    }

    public function setQuantidadeItem($quantidade_item): self
    {
        $this->quantidade_item = $quantidade_item;

        return $this;
    }

    public function getQuantidadeSaldoFinal()
    {
        return $this->quantidade_saldo_final;
    }

    public function setQuantidadeSaldoFinal($quantidade_saldo_final): self
    {
        $this->quantidade_saldo_final = $quantidade_saldo_final;

        return $this;
    }

    public function getValorMovimento()
    {
        return $this->valor_movimento;
    }

    public function setValorMovimento($valor_movimento): self
    {
        $this->valor_movimento = $valor_movimento;

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

    public function getItemContaReceber(): ?ItemContaReceber
    {
        return $this->item_conta_receber;
    }

    public function setItemContaReceber(?ItemContaReceber $item_conta_receber): self
    {
        $this->item_conta_receber = $item_conta_receber;

        return $this;
    }


}
