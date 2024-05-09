<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PlanoContasContaPagarRepository")
 * @ORM\Table(name="plano_contas_conta_pagar")
 */
class PlanoContasContaPagar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaPagar")
     * @ORM\JoinColumn(nullable=true)
     */
    private $conta_pagar;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanoConta")
     * @ORM\JoinColumn(nullable=true)
     */
    private $plano_conta;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $complemento;

    /**
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $numero_sequencia;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContaPagar() : ? ContaPagar
    {
        return $this->conta_pagar;
    }

    public function setContaPagar(ContaPagar $conta_pagar) : self
    {
        $this->conta_pagar = $conta_pagar;

        return $this;
    }

    public function getPlanoConta() : ? PlanoConta
    {
        return $this->plano_conta;
    }

    public function setPlanoConta(PlanoConta $plano_conta) : self
    {
        $this->plano_conta = $plano_conta;

        return $this;
    }

    public function getComplemento() : ? string
    {
        return $this->complemento;
    }

    public function setComplemento(? string $complemento) : self
    {
        $this->complemento = $complemento;

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor) : self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getNumeroSequencia(): ?int
    {
        return $this->numero_sequencia;
    }

    public function setNumeroSequencia(int $numero_sequencia): self
    {
        $this->numero_sequencia = $numero_sequencia;

        return $this;
    }


}
