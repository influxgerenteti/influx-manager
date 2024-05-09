<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ContaReceberRepository")
 * @ORM\Table(name="conta_receber")
 */
class ContaReceber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="franqueadaContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoContaReceber")
     * @ORM\JoinColumn(nullable=true)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa", inversedBy="pessoaContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sacado_pessoa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="contratoContaReceber")
     */
    private $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="usuarioContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="vendedorContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vendedor_funcionario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_emissao;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_total;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao_cancelamento;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"(PEN)dentes, (VEN)cidas, (NEG)ativadas, (LIQ)uidadas"})
     */
    private $situacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemContaReceber", mappedBy="conta_receber")
     */
    private $itemsContaReceber;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bolsista;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="conta_receber")
     */
    private $tituloRecebers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Renegociacao", mappedBy="conta_receber")
     */
    private $renegociacoes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Servico", mappedBy="conta_receber")
     */
    private $servicos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="conta_receber")
     */
    private $reposicaoAulas;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="conta_receber")
     */
    private $atividadeExtras;


    /**
     * @var                       string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;

    public function __construct()
    {
        $this->itemsContaReceber = new ArrayCollection();
        $this->data_emissao      = new \DateTime();
        $this->situacao          = 'ABE';
        $this->bolsista          = false;
        $this->tituloRecebers    = new ArrayCollection();
        $this->renegociacoes     = new ArrayCollection();
        $this->servicos          = new ArrayCollection();
        $this->reposicaoAulas    = new ArrayCollection();
        $this->atividadeExtras   = new ArrayCollection();
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

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

        return $this;
    }

    public function getSacadoPessoa(): ?Pessoa
    {
        return $this->sacado_pessoa;
    }

    public function setSacadoPessoa(?Pessoa $sacado_pessoa): self
    {
        $this->sacado_pessoa = $sacado_pessoa;

        return $this;
    }

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

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

    public function getVendedorFuncionario(): ?Funcionario
    {
        return $this->vendedor_funcionario;
    }

    public function setVendedorFuncionario(?Funcionario $vendedor_funcionario): self
    {
        $this->vendedor_funcionario = $vendedor_funcionario;

        return $this;
    }

    public function getDataEmissao(): ?\DateTimeInterface
    {
        return $this->data_emissao;
    }

    public function setDataEmissao(\DateTimeInterface $data_emissao): self
    {
        $this->data_emissao = $data_emissao;

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

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getObservacaoCancelamento(): ?string
    {
        return $this->observacao_cancelamento;
    }

    public function setObservacaoCancelamento(?string $observacao_cancelamento): self
    {
        $this->observacao_cancelamento = $observacao_cancelamento;

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
    public function getItemsContaReceber(): Collection
    {
        return $this->itemsContaReceber;
    }

    public function addItemsContaReceber(ItemContaReceber $itemsContaReceber): self
    {
        if ($this->itemsContaReceber->contains($itemsContaReceber) === false) {
            $this->itemsContaReceber[] = $itemsContaReceber;
            $itemsContaReceber->setContaReceber($this);
        }

        return $this;
    }

    public function removeItemsContaReceber(ItemContaReceber $itemsContaReceber): self
    {
        if ($this->itemsContaReceber->contains($itemsContaReceber) === true) {
            $this->itemsContaReceber->removeElement($itemsContaReceber);
            // set the owning side to null (unless already changed)
            if ($itemsContaReceber->getContaReceber() === $this) {
                $itemsContaReceber->setContaReceber(null);
            }
        }

        return $this;
    }

    public function getBolsista(): ?bool
    {
        return $this->bolsista;
    }

    public function setBolsista(bool $bolsista): self
    {
        $this->bolsista = $bolsista;

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getTituloRecebers(): Collection
    {
        return $this->tituloRecebers;
    }

    public function addTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === false) {
            $this->tituloRecebers[] = $tituloReceber;
            $tituloReceber->setContaReceber($this);
        }

        return $this;
    }

    public function removeTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === true) {
            $this->tituloRecebers->removeElement($tituloReceber);
            // set the owning side to null (unless already changed)
            if ($tituloReceber->getContaReceber() === $this) {
                $tituloReceber->setContaReceber(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Renegociacao[]
     */
    public function getRenegociacoes(): Collection
    {
        return $this->renegociacoes;
    }

    public function addRenegociacao(Renegociacao $renegociacao): self
    {
        if ($this->renegociacoes->contains($renegociacao) === false) {
            $this->renegociacoes[] = $renegociacao;
            $renegociacao->setContaReceber($this);
        }

        return $this;
    }

    public function removeRenegociacao(Renegociacao $renegociacao): self
    {
        if ($this->renegociacoes->contains($renegociacao) === true) {
            $this->renegociacoes->removeElement($renegociacao);
            // set the owning side to null (unless already changed)
            if ($renegociacao->getContaReceber() === $this) {
                $renegociacao->setContaReceber(null);
            }
        }

        return $this;
    }

    /**
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
            $servico->setContaReceber($this);
        }

        return $this;
    }

    public function removeServico(Servico $servico): self
    {
        if ($this->servicos->contains($servico) === true) {
            $this->servicos->removeElement($servico);
            // set the owning side to null (unless already changed)
            if ($servico->getContaReceber() === $this) {
                $servico->setContaReceber(null);
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
            $reposicaoAula->setContaReceber($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getContaReceber() === $this) {
                $reposicaoAula->setContaReceber(null);
            }
        }

        return $this;
    }

    /**
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
            $atividadeExtra->addContaReceber($this);
        }

        return $this;
    }

    public function removeAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === true) {
            $this->atividadeExtras->removeElement($atividadeExtra);
            $atividadeExtra->removeContaReceber($this);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSponteId(): ?string
    {
        return $this->sponte_id;
    }

    /**
     * @param string|null $sponte_id
     */
    public function setSponteId(?string $sponte_id): void
    {
        $this->sponte_id = $sponte_id;
    }


}
