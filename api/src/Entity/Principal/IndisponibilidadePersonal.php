<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\IndisponibilidadePersonalRepository")
 */
class IndisponibilidadePersonal
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
     * @ORM\Column(type="datetime")
     */
    private $data_inicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_fim;

    /**
     * @ORM\Column(type="time")
     */
    private $hora_inicio;

    /**
     * @ORM\Column(type="time")
     */
    private $hora_fim;

    /**
     * @ORM\Column(type="integer")
     */
    private $dia_semana;

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

    public function getDataInicio(): ?\DateTimeInterface
    {
        return $this->data_inicio;
    }

    public function setDataInicio(\DateTimeInterface $data_inicio): self
    {
        $this->data_inicio = $data_inicio;

        return $this;
    }

    public function getDataFim(): ?\DateTimeInterface
    {
        return $this->data_fim;
    }

    public function setDataFim(\DateTimeInterface $data_fim): self
    {
        $this->data_fim = $data_fim;

        return $this;
    }

    public function getHoraInicio(): ?\DateTimeInterface
    {
        return $this->hora_inicio;
    }

    public function setHoraInicio(\DateTimeInterface $hora_inicio): self
    {
        $this->hora_inicio = $hora_inicio;

        return $this;
    }

    public function getHoraFim(): ?\DateTimeInterface
    {
        return $this->hora_fim;
    }

    public function setHoraFim(\DateTimeInterface $hora_fim): self
    {
        $this->hora_fim = $hora_fim;

        return $this;
    }

    public function getDiaSemana(): ?int
    {
        return $this->dia_semana;
    }

    public function setDiaSemana(int $dia_semana): self
    {
        $this->dia_semana = $dia_semana;

        return $this;
    }


}
