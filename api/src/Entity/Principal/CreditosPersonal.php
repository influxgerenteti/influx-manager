<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CreditosPersonalRepository")
 */
class CreditosPersonal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="creditosPersonal", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrato;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantidade;

    /**
     * @ORM\Column(type="integer")
     */
    private $saldo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendamentoPersonal", mappedBy="creditos_personal", orphanRemoval=true)
     */
    private $agendamentoPersonals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiarioPersonal", mappedBy="creditos_personal")
     */
    private $alunoDiarioPersonals;

    public function __construct()
    {
        $this->agendamentoPersonals = new ArrayCollection();
        $this->alunoDiarioPersonals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(Contrato $contrato): self
    {
        $this->contrato = $contrato;

        return $this;
    }

    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): self
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    public function getSaldo(): ?int
    {
        return $this->saldo;
    }

    public function setSaldo(int $saldo): self
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * @return Collection|AgendamentoPersonal[]
     */
    public function getAgendamentoPersonals(): Collection
    {
        return $this->agendamentoPersonals;
    }

    public function addAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === false) {
            $this->agendamentoPersonals[] = $agendamentoPersonal;
            $agendamentoPersonal->setCreditosPersonal($this);
        }

        return $this;
    }

    public function removeAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === true) {
            $this->agendamentoPersonals->removeElement($agendamentoPersonal);
            // set the owning side to null (unless already changed)
            if ($agendamentoPersonal->getCreditosPersonal() === $this) {
                $agendamentoPersonal->setCreditosPersonal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoDiarioPersonal[]
     */
    public function getAlunoDiarioPersonals(): Collection
    {
        return $this->alunoDiarioPersonals;
    }

    public function addAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === false) {
            $this->alunoDiarioPersonals[] = $alunoDiarioPersonal;
            $alunoDiarioPersonal->setCreditosPersonal($this);
        }

        return $this;
    }

    public function removeAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === true) {
            $this->alunoDiarioPersonals->removeElement($alunoDiarioPersonal);
            // set the owning side to null (unless already changed)
            if ($alunoDiarioPersonal->getCreditosPersonal() === $this) {
                $alunoDiarioPersonal->setCreditosPersonal(null);
            }
        }

        return $this;
    }


}
