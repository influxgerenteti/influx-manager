<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoMovimentoEstoqueRepository")
 * @ORM\Table(name="tipo_movimento_estoque")
 */
class TipoMovimentoEstoque
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
     * @ORM\Column(type="string", length=35)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(type="string", length=1, options={"default":"A", "comment":"A - ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     *
     * @ORM\Column(type="string", length=3, options={"default":"E", "comment":"E - Entrada, S - Saída, AE - Ajuste de Entrada, AS - Ajuste de Saida"})
     */
    private $tipo_movimento = 'E';

    public function getId()
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

    public function getTipoMovimento(): ?string
    {
        return $this->tipo_movimento;
    }

    public function setTipoMovimento(string $tipo_movimento): self
    {
        $this->tipo_movimento = $tipo_movimento;

        return $this;
    }


}
