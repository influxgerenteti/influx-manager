<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="item",                                                 indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\ItemRepository")
 */
class Item
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="franqueada_id", type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricao", type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="finalidade", type="string", length=20, nullable=true)
     */
    private $finalidade;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo_barra", type="string", length=100, nullable=true)
     */
    private $codigo_barra;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kit", type="string", length=10, nullable=true)
     */
    private $kit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valor_custo", type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_custo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valor_venda", type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_venda;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estoque_minimo", type="decimal", precision=9, scale=6, nullable=true)
     */
    private $estoque_minimo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estoque_maximo", type="decimal", precision=9, scale=6, nullable=true)
     */
    private $estoque_maximo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="saldo", type="decimal", precision=9, scale=6, nullable=true)
     */
    private $saldo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="A - Ativo, I - Inativo"})
     */
    private $situacao;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(int $franqueada_id): self
    {
        $this->franqueada_id = $franqueada_id;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getFinalidade(): ?string
    {
        return $this->finalidade;
    }

    public function setFinalidade(?string $finalidade): self
    {
        $this->finalidade = $finalidade;

        return $this;
    }

    public function getCodigoBarra(): ?string
    {
        return $this->codigo_barra;
    }

    public function setCodigoBarra(?string $codigo_barra): self
    {
        $this->codigo_barra = $codigo_barra;

        return $this;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getKit(): ?string
    {
        return $this->kit;
    }

    public function setKit(?string $kit): self
    {
        $this->kit = $kit;

        return $this;
    }

    public function getValorCusto()
    {
        return $this->valor_custo;
    }

    public function setValorCusto($valor_custo): self
    {
        $this->valor_custo = $valor_custo;

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

    public function getEstoqueMinimo()
    {
        return $this->estoque_minimo;
    }

    public function setEstoqueMinimo($estoque_minimo): self
    {
        $this->estoque_minimo = $estoque_minimo;

        return $this;
    }

    public function getEstoqueMaximo()
    {
        return $this->estoque_maximo;
    }

    public function setEstoqueMaximo($estoque_maximo): self
    {
        $this->estoque_maximo = $estoque_maximo;

        return $this;
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function setSaldo($saldo): self
    {
        $this->saldo = $saldo;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(?string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }


}
