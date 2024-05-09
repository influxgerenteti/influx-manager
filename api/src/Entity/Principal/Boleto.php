<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\BoletoRepository")
 */
class Boleto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="boletos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nosso_numero;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_desconto;

    /**
     * @ORM\Column(type="string", length=3, options={"default":"PEN", "comment":"PEN - Pendente de Envio, ENV - Enviado, CON - Confirmado, REJ - Rejeitado, REC - Recebimento, DEV - Devolvido, CAN - Cancelado"})
     */
    private $situacao_cobranca = 'PEN';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Conta", inversedBy="boletos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $titulo_receber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\MovimentoConta", inversedBy="boleto", cascade={"persist", "remove"})
     */
    private $movimento_conta;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $valor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_vencimento;

    public function __construct()
    {
        $this->tituloRecebers = new ArrayCollection();
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

    public function getNossoNumero(): ?string
    {
        return $this->nosso_numero;
    }

    public function setNossoNumero(string $nosso_numero): self
    {
        $this->nosso_numero = $nosso_numero;

        return $this;
    }

    public function getValorDesconto()
    {
        return $this->valor_desconto;
    }

    public function setValorDesconto($valor_desconto): self
    {
        $this->valor_desconto = $valor_desconto;

        return $this;
    }

    public function getSituacaoCobranca(): ?string
    {
        return $this->situacao_cobranca;
    }

    public function setSituacaoCobranca(?string $situacao_cobranca): self
    {
        $this->situacao_cobranca = $situacao_cobranca;

        return $this;
    }

    public function getConta(): ?Conta
    {
        return $this->conta;
    }

    public function setConta(?Conta $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    public function getTituloReceber(): ?TituloReceber
    {
        return $this->titulo_receber;
    }

    public function setTituloReceber(?TituloReceber $titulo_receber): self
    {
        $this->titulo_receber = $titulo_receber;

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
            $tituloReceber->setBoleto($this);
        }

        return $this;
    }

    public function removeTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === true) {
            $this->tituloRecebers->removeElement($tituloReceber);
            // set the owning side to null (unless already changed)
            if ($tituloReceber->getBoleto() === $this) {
                $tituloReceber->setBoleto(null);
            }
        }

        return $this;
    }

    public function getMovimentoConta(): ?MovimentoConta
    {
        return $this->movimento_conta;
    }

    public function setMovimentoConta(?MovimentoConta $movimento_conta): self
    {
        $this->movimento_conta = $movimento_conta;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getDataVencimento(): ?\DateTimeInterface
    {
        return $this->data_vencimento;
    }

    public function setDataVencimento(\DateTimeInterface $data_vencimento): self
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }


}
