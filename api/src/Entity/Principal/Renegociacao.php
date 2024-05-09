<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\RenegociacaoRepository")
 */
class Renegociacao
{
    /**
     * Joins in the "browse" view
     */
    public static $browseJoins = ['responsavel_financeiro_pessoa'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaReceber", inversedBy="renegociacoes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta_receber;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\TituloReceber")
     */
    private $titulos_receber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa")
     * @ORM\JoinColumn(nullable=false)
     * (label="Responsável Financeiro", showOnBrowse="true", descriptionColumn="nome_contato", valueColumn="id")
     */
    private $responsavel_financeiro_pessoa;

    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     * (label="Data de criação", showOnBrowse="true", format="datetime")
     */
    private $data_criacao;

    public function __construct()
    {
        $this->data_criacao    = new \DateTime();
        $this->titulos_receber = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContaReceber(): ?ContaReceber
    {
        return $this->conta_receber;
    }

    public function setContaReceber(?ContaReceber $conta_receber): self
    {
        $this->conta_receber = $conta_receber;

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getTitulosReceber(): Collection
    {
        return $this->titulos_receber;
    }

    public function addTitulosReceber(TituloReceber $titulosReceber): self
    {
        if ($this->titulos_receber->contains($titulosReceber) === false) {
            $this->titulos_receber[] = $titulosReceber;
        }

        return $this;
    }

    public function removeTitulosReceber(TituloReceber $titulosReceber): self
    {
        if ($this->titulos_receber->contains($titulosReceber) === true) {
            $this->titulos_receber->removeElement($titulosReceber);
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

    public function getResponsavelFinanceiroPessoa(): ?Pessoa
    {
        return $this->responsavel_financeiro_pessoa;
    }

    public function setResponsavelFinanceiroPessoa(?Pessoa $responsavel_financeiro_pessoa): self
    {
        $this->responsavel_financeiro_pessoa = $responsavel_financeiro_pessoa;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }


}
