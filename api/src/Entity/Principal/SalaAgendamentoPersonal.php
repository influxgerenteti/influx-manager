<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\SalaAgendamentoRepository")
 */
class SalaAgendamentoPersonal
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
     * @ORM\Column(type="string", length=3)
     */
    private $dia_semana;

    /**
     * @ORM\Column(type="time")
     */
    private $hora_inicial;

    /**
     * @ORM\Column(type="time")
     */
    private $hora_final;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada", inversedBy="salaAgendamentoPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala_franqueada;


    public function __construct()
    {

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

    public function getSalaFranqueada() : ?SalaFranqueada
    {
        return $this->sala_franqueada;
    }

    public function setSalaFranqueada(?SalaFranqueada $sala_franqueada): self
    {
        $this->sala_franqueada = $sala_franqueada;
        return $this;
    }

    public function getDiaSemana(): ?string
    {
        return $this->dia_semana;
    }

    public function setDiaSemana(string $dia_semana): self
    {
        $this->dia_semana = $dia_semana;

        return $this;
    }

    public function getHoraInicial(): ?\DateTimeInterface
    {
        return $this->hora_inicial;
    }

    public function setHoraInicial(\DateTimeInterface $hora_inicial): self
    {
        $this->hora_inicial = $hora_inicial;

        return $this;
    }

    public function getHoraFinal(): ?\DateTimeInterface
    {
        return $this->hora_final;
    }

    public function setHoraFinal(\DateTimeInterface $hora_final): self
    {
        $this->hora_final = $hora_final;

        return $this;
    }
}
