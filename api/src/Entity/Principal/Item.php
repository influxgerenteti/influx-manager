<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ItemRepository")
 */
class Item
{


    public function __construct()
    {
        $this->data_cadastro         = new \DateTime();
        $this->itemsItemContaReceber = new ArrayCollection();
        $this->atividadeExtras       = new ArrayCollection();
        $this->servicos          = new ArrayCollection();
        $this->reposicaoAulas    = new ArrayCollection();
        $this->movimentoEstoques = new ArrayCollection();
        $this->cursos            = new ArrayCollection();
        $this->itemFranqueadas   = new ArrayCollection();
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
     * @ORM\Column(type="string", length=3, nullable=true, options={"comment":"KG,UN"})
     */
    private $unidade_medida;

    /**
     *
     * @ORM\Column(type="string", length=80)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $narrativa;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_cadastro;

    /**
     *
     * @ORM\Column(type="string", length=2, options={"default":"A"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemContaReceber", mappedBy="item")
     */
    private $itemsItemContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoEstoque", mappedBy="item")
     */
    private $movimentoEstoques;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanoConta")
     */
    private $plano_conta;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="item")
     */
    private $atividadeExtras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Servico", mappedBy="item")
     */
    private $servicos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="item")
     */
    private $reposicaoAulas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoItem", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo_item;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Curso", mappedBy="servico")
     */
    private $cursos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemFranqueada", mappedBy="item")
     */
    private $itemFranqueadas;

    public function getId()
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

    public function getUnidadeMedida(): ?string
    {
        return $this->unidade_medida;
    }

    public function setUnidadeMedida(?string $unidade_medida): self
    {
        $this->unidade_medida = $unidade_medida;

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

    public function getNarrativa(): ?string
    {
        return $this->narrativa;
    }

    public function setNarrativa(?string $narrativa): self
    {
        $this->narrativa = $narrativa;

        return $this;
    }

    public function getDataCadastro(): ?\DateTimeInterface
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(\DateTimeInterface $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;

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
     * @return Collection|ItemContaReceber[]
     */
    public function getItemsItemContaReceber(): Collection
    {
        return $this->itemsItemContaReceber;
    }

    public function addItemsItemContaReceber(ItemContaReceber $itemsItemContaReceber): self
    {
        if ($this->itemsItemContaReceber->contains($itemsItemContaReceber) === false) {
            $this->itemsItemContaReceber[] = $itemsItemContaReceber;
            $itemsItemContaReceber->setItem($this);
        }

        return $this;
    }

    public function removeItemsItemContaReceber(ItemContaReceber $itemsItemContaReceber): self
    {
        if ($this->itemsItemContaReceber->contains($itemsItemContaReceber) === true) {
            $this->itemsItemContaReceber->removeElement($itemsItemContaReceber);
            // set the owning side to null (unless already changed)
            if ($itemsItemContaReceber->getItem() === $this) {
                $itemsItemContaReceber->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemContaReceber[]
     */
    public function getMovimentoEstoques(): Collection
    {
        return $this->movimentoEstoques;
    }

    public function addMovimentoEstoque(MovimentoEstoque $movimentoEstoque): self
    {
        if ($this->movimentoEstoques->contains($movimentoEstoque) === false) {
            $this->movimentoEstoques[] = $movimentoEstoque;
            $movimentoEstoque->setItem($this);
        }

        return $this;
    }

    public function removeMovimentoEstoque(MovimentoEstoque $movimentoEstoque): self
    {
        if ($this->movimentoEstoques->contains($movimentoEstoque) === true) {
            $this->movimentoEstoques->removeElement($movimentoEstoque);
            // set the owning side to null (unless already changed)
            if ($movimentoEstoque->getItem() === $this) {
                $movimentoEstoque->setItem(null);
            }
        }

        return $this;
    }

    public function getPlanoConta(): ?PlanoConta
    {
        return $this->plano_conta;
    }

    public function setPlanoConta(?PlanoConta $plano_conta): self
    {
        $this->plano_conta = $plano_conta;

        return $this;
    }

    /**
     *
     * @return Collection|AtividadeExtra[]
     */
    public function getAtividadeExtras(): Collection
    {
        return $this->atividadeExtras;
    }

    public function addAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === false) {
            $this->atividadeExtras[] = $atividadeExtra;
            $atividadeExtra->setItem($this);
        }

        return $this;
    }

    public function removeAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === true) {
            $this->atividadeExtras->removeElement($atividadeExtra);
            // set the owning side to null (unless already changed)
            if ($atividadeExtra->getItem() === $this) {
                $atividadeExtra->setItem(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Servico[]
     */
    public function getServicos(): Collection
    {
        return $this->servicos;
    }

    public function addServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === false) {
            $this->servicos[] = $servico;
            $servico->setItem($this);
        }

        return $this;
    }

    public function removeServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === true) {
            $this->servicos->removeElement($servico);
            // set the owning side to null (unless already changed)
            if ($servico->getItem() === $this) {
                $servico->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulas(): Collection
    {
        return $this->reposicaoAulas;
    }

    public function addReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === false) {
            $this->reposicaoAulas[] = $reposicaoAula;
            $reposicaoAula->setItem($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getItem() === $this) {
                $reposicaoAula->setItem(null);
            }
        }

        return $this;
    }

    public function getTipoItem(): ?TipoItem
    {
        return $this->tipo_item;
    }

    public function setTipoItem(?TipoItem $tipo_item): self
    {
        $this->tipo_item = $tipo_item;

        return $this;
    }

    /**
     * @return Collection|Curso[]
     */
    public function getCursos(): Collection
    {
        return $this->cursos;
    }

    public function addCurso(Curso $curso): self
    {
        if ($this->cursos->contains($curso) === false) {
            $this->cursos[] = $curso;
            $curso->setServico($this);
        }

        return $this;
    }

    public function removeCurso(Curso $curso): self
    {
        if ($this->cursos->contains($curso) === true) {
            $this->cursos->removeElement($curso);
            // set the owning side to null (unless already changed)
            if ($curso->getServico() === $this) {
                $curso->setServico(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemFranqueada[]
     */
    public function getItemFranqueadas(): Collection
    {
        return $this->itemFranqueadas;
    }

    public function addItemFranqueada(ItemFranqueada $itemFranqueada): self
    {
        if ($this->itemFranqueadas->contains($itemFranqueada) === false) {
            $this->itemFranqueadas[] = $itemFranqueada;
            $itemFranqueada->setItem($this);
        }

        return $this;
    }

    public function removeItemFranqueada(ItemFranqueada $itemFranqueada): self
    {
        if ($this->itemFranqueadas->contains($itemFranqueada) === true) {
            $this->itemFranqueadas->removeElement($itemFranqueada);
            // set the owning side to null (unless already changed)
            if ($itemFranqueada->getItem() === $this) {
                $itemFranqueada->setItem(null);
            }
        }

        return $this;
    }


}
