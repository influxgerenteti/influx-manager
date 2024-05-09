<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\HorarioAulaRepository")
 * @ORM\Table(name="horario_aula")
 */
class HorarioAula
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"DOM, SEG, TER, QUA, QUI, SEX, SAB apenas"})
     */
    private $dia_semana;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Horario", inversedBy="horarioAulas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $horario;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $horario_inicio;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHorario(): ?Horario
    {
        return $this->horario;
    }

    public function setHorario(?Horario $horario): self
    {
        $this->horario = $horario;

        return $this;
    }

    public function getHorarioInicio(): ?\DateTimeInterface
    {
        return $this->horario_inicio;
    }

    public function setHorarioInicio(?\DateTimeInterface $horario_inicio): self
    {
        $this->horario_inicio = $horario_inicio;

        return $this;
    }


}
