<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MovimentoDollarRepository")
 */
class MovimentoDollar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AtividadeDollar")
     * @ORM\JoinColumn(nullable=true)
     */
    private $atividade_dollar;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_movimento;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default"="C","fixed"=true,"comment"="C - Crédito, D - Débito, S - Saque"})
     */
    private $tipo_operacao = "C";

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=false)
     */
    private $valor;

    public function __construct()
    {
        $this->data_movimento = new \DateTime();
    }

    public function __clone()
    {
        if (is_null($this->id) === false) {
            $this->id = null;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

        return $this;
    }

    public function getAtividadeDollar(): ?AtividadeDollar
    {
        return $this->atividade_dollar;
    }

    public function setAtividadeDollar(?AtividadeDollar $atividade_dollar): self
    {
        $this->atividade_dollar = $atividade_dollar;

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

    public function getDataMovimento(): ?\DateTimeInterface
    {
        return $this->data_movimento;
    }

    public function setDataMovimento(\DateTimeInterface $data_movimento): self
    {
        $this->data_movimento = $data_movimento;

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

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }


}
