<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CalendarioRepository")
 * @ORM\Table(name="calendario")
 */
class Calendario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_inicial;

    /**
     * @ORM\Column(type="boolean", options={"default":"0", "comment":"se for true, influência na conta de débiutos e juros"})
     */
    private $feriado_bancario = false;

    /**
     * @ORM\Column(type="boolean", options={"default":"0", "comment":"se for true, influência no calendário das aulas"})
     */
    private $dia_letivo = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_final = null;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $feriado_anual = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="calendarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDataInicial(): ?\DateTimeInterface
    {
        return $this->data_inicial;
    }

    public function setDataInicial(\DateTimeInterface $data_inicial): self
    {
        $this->data_inicial = $data_inicial;

        return $this;
    }

    public function getFeriadoBancario(): ?bool
    {
        return $this->feriado_bancario;
    }

    public function setFeriadoBancario(bool $feriado_bancario): self
    {
        $this->feriado_bancario = $feriado_bancario;

        return $this;
    }

    public function getDiaLetivo(): ?bool
    {
        return $this->dia_letivo;
    }

    public function setDiaLetivo(bool $dia_letivo): self
    {
        $this->dia_letivo = $dia_letivo;

        return $this;
    }

    public function getDataFinal(): ?\DateTimeInterface
    {
        return $this->data_final;
    }

    public function setDataFinal(?\DateTimeInterface $data_final): self
    {
        $this->data_final = $data_final;

        return $this;
    }

    public function getFeriadoAnual(): ?bool
    {
        return $this->feriado_anual;
    }

    public function setFeriadoAnual(bool $feriado_anual): self
    {
        $this->feriado_anual = $feriado_anual;

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


}
