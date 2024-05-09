<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ValorHoraRepository")
 * @ORM\Table(name="valor_hora")
 */
class ValorHora
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ValorHoraLinhas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $valor_hora_linhas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\NivelInstrutor", inversedBy="valorHoras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $nivel_instrutor;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $valor;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A", "comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = "A";

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 1})
     */
    private $valor_bonus = 1;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_extra = 0;

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

    public function getValorHoraLinhas(): ?ValorHoraLinhas
    {
        return $this->valor_hora_linhas;
    }

    public function setValorHoraLinhas(?ValorHoraLinhas $valor_hora_linhas): self
    {
        $this->valor_hora_linhas = $valor_hora_linhas;

        return $this;
    }

    public function getNivelInstrutor(): ?NivelInstrutor
    {
        return $this->nivel_instrutor;
    }

    public function setNivelInstrutor(?NivelInstrutor $nivel_instrutor): self
    {
        $this->nivel_instrutor = $nivel_instrutor;

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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getValorBonus()
    {
        return $this->valor_bonus;
    }

    public function setValorBonus($valor_bonus): self
    {
        $this->valor_bonus = $valor_bonus;

        return $this;
    }

    public function getValorExtra()
    {
        return $this->valor_extra;
    }

    public function setValorExtra($valor_extra): self
    {
        $this->valor_extra = $valor_extra;

        return $this;
    }


}
