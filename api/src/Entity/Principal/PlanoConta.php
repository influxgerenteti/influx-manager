<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PlanoContaRepository")
 * @ORM\Table(name="plano_conta")
 */
class PlanoConta
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     *
     * @ORM\Column(type="string", length=1, options={"comment":"E - Entrada, S - Saída"})
     */
    private $tipo_movimento_nota;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanoConta")
     */
    private $pai;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Pessoa", mappedBy="plano_conta")
     */
    private $pessoas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="planoContas")
     */
    private $franqueada;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemContaReceber", mappedBy="plano_conta")
     */
    private $itemsContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ParcelamentoOperadoraCartao", mappedBy="plano_conta")
     */
    private $parcelamentoOperadoraCartaos;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $chave_consulta;

    public function __construct()
    {
        $this->pessoas           = new ArrayCollection();
        $this->itemsContaReceber = new ArrayCollection();
        $this->parcelamentoOperadoraCartaos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescricao() : ? string
    {
        return $this->descricao;
    }

    public function setDescricao(? string $descricao) : self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getSituacao() : ? string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao) : self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getTipoMovimentoNota() : ? string
    {
        return $this->tipo_movimento_nota;
    }

    public function setTipoMovimentoNota(string $tipo_movimento_nota) : self
    {
        $this->tipo_movimento_nota = $tipo_movimento_nota;

        return $this;
    }

    public function getPai() : ? self
    {
        return $this->pai;
    }

    public function setPai(? self $pai) : self
    {
        $this->pai = $pai;

        return $this;
    }

    /**
     * @return Collection|Pessoa[]
     */
    public function getPessoas(): Collection
    {
        return $this->pessoas;
    }

    public function addPessoa(Pessoa $pessoa): self
    {
        if ($this->pessoas->contains($pessoa) === false) {
            $this->pessoas[] = $pessoa;
            $pessoa->setPlanoConta($this);
        }

        return $this;
    }

    public function removePessoa(Pessoa $pessoa): self
    {
        if ($this->pessoas->contains($pessoa) === true) {
            $this->pessoas->removeElement($pessoa);
            // set the owning side to null (unless already changed)
            if ($pessoa->getPlanoConta() === $this) {
                $pessoa->setPlanoConta(null);
            }
        }

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

    /**
     * @return Collection|ItemContaReceber[]
     */
    public function getItemsContaReceber(): Collection
    {
        return $this->itemsContaReceber;
    }

    public function addItemsContaReceber(ItemContaReceber $itemsContaReceber): self
    {
        if ($this->itemsContaReceber->contains($itemsContaReceber) === false) {
            $this->itemsContaReceber[] = $itemsContaReceber;
            $itemsContaReceber->setPlanoConta($this);
        }

        return $this;
    }

    public function removeItemsContaReceber(ItemContaReceber $itemsContaReceber): self
    {
        if ($this->itemsContaReceber->contains($itemsContaReceber) === true) {
            $this->itemsContaReceber->removeElement($itemsContaReceber);
            // set the owning side to null (unless already changed)
            if ($itemsContaReceber->getPlanoConta() === $this) {
                $itemsContaReceber->setPlanoConta(null);
            }
        }

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
            $parcelamentoOperadoraCartao->setPlanoConta($this);
        }

        return $this;
    }

    public function removeParcelamentoOperadoraCartao(ParcelamentoOperadoraCartao $parcelamentoOperadoraCartao): self
    {
        if ($this->parcelamentoOperadoraCartaos->contains($parcelamentoOperadoraCartao) === true) {
            $this->parcelamentoOperadoraCartaos->removeElement($parcelamentoOperadoraCartao);
            // set the owning side to null (unless already changed)
            if ($parcelamentoOperadoraCartao->getPlanoConta() === $this) {
                $parcelamentoOperadoraCartao->setPlanoConta(null);
            }
        }

        return $this;
    }

    public function getChaveConsulta(): ?string
    {
        return $this->chave_consulta;
    }

    public function setChaveConsulta(?string $chave_consulta): self
    {
        $this->chave_consulta = $chave_consulta;

        return $this;
    }


}
