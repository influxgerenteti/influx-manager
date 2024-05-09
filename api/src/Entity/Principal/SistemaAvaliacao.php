<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\SistemaAvaliacaoRepository")
 * @ORM\Table(name="sistema_avaliacao")
 */
class SistemaAvaliacao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conceito_aprovacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conceito_corte_compromisso_qualidade;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     */
    private $frequencia_minima;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     */
    private $frequencia_corte_compromisso_qualidade;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     */
    private $nota_aprovacao;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     */
    private $nota_corte_compromisso_qualidade;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $excluido = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConceitoAprovacao(): ?ConceitoAvaliacao
    {
        return $this->conceito_aprovacao;
    }

    public function setConceitoAprovacao(?ConceitoAvaliacao $conceito_aprovacao): self
    {
        $this->conceito_aprovacao = $conceito_aprovacao;

        return $this;
    }

    public function getConceitoCorteCompromissoQualidade(): ?ConceitoAvaliacao
    {
        return $this->conceito_corte_compromisso_qualidade;
    }

    public function setConceitoCorteCompromissoQualidade(?ConceitoAvaliacao $conceito_corte_compromisso_qualidade): self
    {
        $this->conceito_corte_compromisso_qualidade = $conceito_corte_compromisso_qualidade;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getFrequenciaMinima()
    {
        return $this->frequencia_minima;
    }

    public function setFrequenciaMinima($frequencia_minima): self
    {
        $this->frequencia_minima = $frequencia_minima;

        return $this;
    }

    public function getFrequenciaCorteCompromissoQualidade()
    {
        return $this->frequencia_corte_compromisso_qualidade;
    }

    public function setFrequenciaCorteCompromissoQualidade($frequencia_corte_compromisso_qualidade): self
    {
        $this->frequencia_corte_compromisso_qualidade = $frequencia_corte_compromisso_qualidade;

        return $this;
    }

    public function getNotaAprovacao()
    {
        return $this->nota_aprovacao;
    }

    public function setNotaAprovacao($nota_aprovacao): self
    {
        $this->nota_aprovacao = $nota_aprovacao;

        return $this;
    }

    public function getNotaCorteCompromissoQualidade()
    {
        return $this->nota_corte_compromisso_qualidade;
    }

    public function setNotaCorteCompromissoQualidade($nota_corte_compromisso_qualidade): self
    {
        $this->nota_corte_compromisso_qualidade = $nota_corte_compromisso_qualidade;

        return $this;
    }

    public function getExcluido(): ?bool
    {
        return $this->excluido;
    }

    public function setExcluido(bool $excluido): self
    {
        $this->excluido = $excluido;

        return $this;
    }


}
