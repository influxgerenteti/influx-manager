<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\DescontoSuperAmigoRepository")
 */
class DescontoSuperAmigo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome_desconto;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $valor_original;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $valor_saldo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="descontoSuperAmigos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="descontoSuperAmigos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\Column(type="boolean")
     */
    private $indicador;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finalizado;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoConta", mappedBy="desconto_super_amigos")
     */
    private $movimentoContas;

    public function __construct()
    {
        $this->movimentoContas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomeDesconto(): ?string
    {
        return $this->nome_desconto;
    }

    public function setNomeDesconto(string $nome_desconto): self
    {
        $this->nome_desconto = $nome_desconto;

        return $this;
    }

    public function getValorOriginal()
    {
        return $this->valor_original;
    }

    public function setValorOriginal($valor_original): self
    {
        $this->valor_original = $valor_original;

        return $this;
    }

    public function getValorSaldo()
    {
        return $this->valor_saldo;
    }

    public function setValorSaldo($valor_saldo): self
    {
        $this->valor_saldo = $valor_saldo;

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

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

        return $this;
    }

    public function getIndicador(): ?bool
    {
        return $this->indicador;
    }

    public function setIndicador(bool $indicador): self
    {
        $this->indicador = $indicador;

        return $this;
    }

    public function getFinalizado(): ?bool
    {
        return $this->finalizado;
    }

    public function setFinalizado(bool $finalizado): self
    {
        $this->finalizado = $finalizado;

        return $this;
    }

    /**
     * @return Collection|MovimentoConta[]
     */
    public function getMovimentoContas(): Collection
    {
        return $this->movimentoContas;
    }

    public function addMovimentoConta(MovimentoConta $movimentoConta): self
    {
        if ($this->movimentoContas->contains($movimentoConta) === false) {
            $this->movimentoContas[] = $movimentoConta;
            $movimentoConta->setDescontoSuperAmigos($this);
        }

        return $this;
    }

    public function removeMovimentoConta(MovimentoConta $movimentoConta): self
    {
        if ($this->movimentoContas->contains($movimentoConta) === true) {
            $this->movimentoContas->removeElement($movimentoConta);
            // set the owning side to null (unless already changed)
            if ($movimentoConta->getDescontoSuperAmigos() === $this) {
                $movimentoConta->setDescontoSuperAmigos(null);
            }
        }

        return $this;
    }


}
