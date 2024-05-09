<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TransferenciaBancariaRepository")
 */
class TransferenciaBancaria
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="transferenciaBancarias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloReceber", inversedBy="transferenciaBancarias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $titulo_receber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\MovimentoConta", inversedBy="transferencia_bancaria", cascade={"persist", "remove"})
     */
    private $movimento_conta;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $agencia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $conta;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $tipo_transacao;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $valor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_estorno;

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

    public function getTituloReceber(): ?TituloReceber
    {
        return $this->titulo_receber;
    }

    public function setTituloReceber(?TituloReceber $titulo_receber): self
    {
        $this->titulo_receber = $titulo_receber;

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

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(string $agencia): self
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function setConta(string $conta): self
    {
        $this->conta = $conta;

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

    public function getTipoTransacao(): ?string
    {
        return $this->tipo_transacao;
    }

    public function setTipoTransacao(string $tipo_transacao): self
    {
        $this->tipo_transacao = $tipo_transacao;

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getDataEstorno(): ?\DateTimeInterface
    {
        return $this->data_estorno;
    }

    public function setDataEstorno(?\DateTimeInterface $data_estorno): self
    {
        $this->data_estorno = $data_estorno;

        return $this;
    }


}
