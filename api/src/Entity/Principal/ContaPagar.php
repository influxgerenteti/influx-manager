<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ContaPagarRepository")
 * @ORM\Table(name="conta_pagar")
 */
class ContaPagar
{


    public function __construct()
    {
        $this->titulo_pagar = new ArrayCollection();
        $this->plano_contas_conta_pagar = new ArrayCollection();
        $this->data_movimento           = new \DateTime();
        $this->data_emissao          = new \DateTime();
        $this->pagamentoFuncionarios = new ArrayCollection();
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="contasPagar")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fornecedor_pessoa;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_movimento;

    /**
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_total;

    /**
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_parcela;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_parcelas;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PlanoContasContaPagar", mappedBy="conta_pagar")
     */
    private $plano_contas_conta_pagar;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloPagar", mappedBy="conta_pagar")
     */
    private $titulo_pagar;

     /**
     * @var                       string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;

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

    public function getFornecedorPessoa(): ?Pessoa
    {
        return $this->fornecedor_pessoa;
    }

    public function setFornecedorPessoa(?Pessoa $fornecedor_pessoa): self
    {
        $this->fornecedor_pessoa = $fornecedor_pessoa;

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

    public function getDataMovimento(): ?\DateTimeInterface
    {
        return $this->data_movimento;
    }

    public function setDataMovimento(\DateTimeInterface $data_movimento): self
    {
        $this->data_movimento = $data_movimento;

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

    public function getValorParcela()
    {
        return $this->valor_parcela;
    }

    public function setValorParcela($valor_parcela): self
    {
        $this->valor_parcela = $valor_parcela;

        return $this;
    }

    public function getNumeroParcelas()
    {
        return $this->numero_parcelas;
    }

    public function setNumeroParcelas($numero_parcelas): self
    {
        $this->numero_parcelas = $numero_parcelas;

        return $this;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;
        return $this;
    }

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento")
     * @ORM\JoinColumn(nullable=false)
     */
    private $forma_cobranca;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoFuncionario", mappedBy="conta_pagar")
     */
    private $pagamentoFuncionarios;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $situacao = 'PEN';

    /**
     *
     * @return Collection|PlanoContasContaPagar[]
     */
    public function getPlanoContasContaPagar() : Collection
    {
        return $this->plano_contas_conta_pagar;
    }

    public function addPlanoContasContaPagar(PlanoContasContaPagar $planoContasContaPagar) : self
    {
        if ($this->plano_contas_conta_pagar->contains($planoContasContaPagar) === false) {
            $this->plano_contas_conta_pagar[] = $planoContasContaPagar;
            $planoContasContaPagar->setContaPagar($this);
        }

        return $this;
    }

    public function removePlanoContasContaPagar(PlanoContasContaPagar $planoContasContaPagar) : self
    {
        if ($this->plano_contas_conta_pagar->contains($planoContasContaPagar) === true) {
            $this->plano_contas_conta_pagar->removeElement($planoContasContaPagar);
            // set the owning side to null (unless already changed)
            if ($planoContasContaPagar->getContaPagar() === $this) {
                $planoContasContaPagar->setContaPagar(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|TituloPagar[]
     */
    public function getTituloPagar() : Collection
    {
        return $this->titulo_pagar;
    }

    public function addTituloPagar(TituloPagar $tituloPagar) : self
    {
        if ($this->titulo_pagar->contains($tituloPagar) === false) {
            $this->titulo_pagar[] = $tituloPagar;
            $tituloPagar->setContaPagar($this);
        }

        return $this;
    }

    public function removeTituloPagar(TituloPagar $tituloPagar) : self
    {
        if ($this->titulo_pagar->contains($tituloPagar) === true) {
            $this->titulo_pagar->removeElement($tituloPagar);
            // set the owning side to null (unless already changed)
            if ($tituloPagar->getContaPagar() === $this) {
                $tituloPagar->setContaPagar(null);
            }
        }

        return $this;
    }

    public function getFormaCobranca() : ? FormaPagamento
    {
        return $this->forma_cobranca;
    }

    public function setFormaCobranca(? FormaPagamento $forma_cobranca) : self
    {
        $this->forma_cobranca = $forma_cobranca;

        return $this;
    }

    /**
     * @return Collection|PagamentoFuncionario[]
     */
    public function getPagamentoFuncionarios(): Collection
    {
        return $this->pagamentoFuncionarios;
    }

    public function addPagamentoFuncionario(PagamentoFuncionario $pagamentoFuncionario): self
    {
        if ($this->pagamentoFuncionarios->contains($pagamentoFuncionario) === false) {
            $this->pagamentoFuncionarios[] = $pagamentoFuncionario;
            $pagamentoFuncionario->setContaPagar($this);
        }

        return $this;
    }

    public function removePagamentoFuncionario(PagamentoFuncionario $pagamentoFuncionario): self
    {
        if ($this->pagamentoFuncionarios->contains($pagamentoFuncionario) === true) {
            $this->pagamentoFuncionarios->removeElement($pagamentoFuncionario);
            // set the owning side to null (unless already changed)
            if ($pagamentoFuncionario->getContaPagar() === $this) {
                $pagamentoFuncionario->setContaPagar(null);
            }
        }

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
