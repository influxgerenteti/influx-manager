<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FuncionarioDisponibilidadeRepository")
 * @ORM\Table(name="funcionario_disponibilidade")
 */
class FuncionarioDisponibilidade
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="disponibilidades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

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
