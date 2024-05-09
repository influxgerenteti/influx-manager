<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ConvidadoAtividadeExtraRepository")
 */
class ConvidadoAtividadeExtra
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
    private $nome;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $presenca;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="convidadoAtividadeExtras")
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AtividadeExtra", inversedBy="convidadoAtividadeExtras")
     */
    private $atividade_extra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getPresenca(): ?string
    {
        return $this->presenca;
    }

    public function setPresenca(?string $presenca): self
    {
        $this->presenca = $presenca;

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

    public function getAtividadeExtra(): ?AtividadeExtra
    {
        return $this->atividade_extra;
    }

    public function setAtividadeExtra(?AtividadeExtra $atividade_extra): self
    {
        $this->atividade_extra = $atividade_extra;

        return $this;
    }


}
