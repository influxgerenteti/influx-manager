<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoMovimentoContaRepository")
 * @ORM\Table(name="tipo_movimento_conta")
 */
class TipoMovimentoConta
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
     * @ORM\Column(type="string", length=2, options={"comment":"D - DÉBITO, C - CRÉDITO, T - TRANSFERENCIA(TEM QUE FAZER DOIS LANÇAMENTOS)"})
     */
    private $tipo_operacao;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default": "0"})
     */
    private $reservado = false;

    /**
     *
     * @ORM\Column(type="string", length=2, options={"default": "A"})
     */
    private $situacao = 'A';

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

    public function getTipoOperacao(): ?string
    {
        return $this->tipo_operacao;
    }

    public function setTipoOperacao(string $tipo_operacao): self
    {
        $this->tipo_operacao = $tipo_operacao;

        return $this;
    }

    public function setReservado(bool $reservado): self
    {
        $this->reservado = $reservado;

        return $this;
    }

    public function getReservado(): ?bool
    {
        return $this->reservado;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }


}
