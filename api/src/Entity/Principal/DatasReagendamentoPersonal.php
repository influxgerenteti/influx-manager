<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\DatasReagendamentoPersonalRepository")
 */
class DatasReagendamentoPersonal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AgendamentoPersonal", inversedBy="datasReagendamentoPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agendamento_personal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_solicitacao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_reagendada;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ultimo_reagendamento;

    function __construct()
    {
        $this->data_solicitacao     = new \DateTime();
        $this->ultimo_reagendamento = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgendamentoPersonal(): ?AgendamentoPersonal
    {
        return $this->agendamento_personal;
    }

    public function setAgendamentoPersonal(?AgendamentoPersonal $agendamento_personal): self
    {
        $this->agendamento_personal = $agendamento_personal;

        return $this;
    }

    public function getDataSolicitacao(): ?\DateTimeInterface
    {
        return $this->data_solicitacao;
    }

    public function setDataSolicitacao(\DateTimeInterface $data_solicitacao): self
    {
        $this->data_solicitacao = $data_solicitacao;

        return $this;
    }

    public function getDataReagendada(): ?\DateTimeInterface
    {
        return $this->data_reagendada;
    }

    public function setDataReagendada(\DateTimeInterface $data_reagendada): self
    {
        $this->data_reagendada = $data_reagendada;

        return $this;
    }

    public function getUltimoReagendamento(): ?bool
    {
        return $this->ultimo_reagendamento;
    }

    public function setUltimoReagendamento(bool $ultimo_reagendamento): self
    {
        $this->ultimo_reagendamento = $ultimo_reagendamento;

        return $this;
    }


}
