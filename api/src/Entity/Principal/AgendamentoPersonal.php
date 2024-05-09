<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AgendamentoPersonalRepository")
 */
class AgendamentoPersonal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="agendamentoPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\CreditosPersonal", inversedBy="agendamentoPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creditos_personal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $inicio;


    /**
     * @ORM\Column(type="datetime")
     */
    private $data_aula;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reagendado;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\AlunoDiarioPersonal", mappedBy="agendamento_personal", cascade={"persist", "remove"})
     */
    private $alunoDiarioPersonal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="agendamentoPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada", inversedBy="agendamentoPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala_franqueada;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finalizado = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\DatasReagendamentoPersonal", mappedBy="agendamento_personal")
     */
    private $datasReagendamentoPersonals;

    public function __construct()
    {
        $this->datasReagendamentoPersonals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

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

    public function getCreditosPersonal(): ?CreditosPersonal
    {
        return $this->creditos_personal;
    }

    public function setCreditosPersonal(?CreditosPersonal $creditos_personal): self
    {
        $this->creditos_personal = $creditos_personal;

        return $this;
    }

    public function getInicio(): ?\DateTimeInterface
    {
        return $this->inicio;
    }

    public function setInicio(\DateTimeInterface $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getDataAula(): ?\DateTimeInterface
    {
        return $this->data_aula;
    }

    public function setDataAula(\DateTimeInterface $data_aula): self
    {
        $this->data_aula = $data_aula;

        return $this;
    }


    public function getReagendado(): ?bool
    {
        return $this->reagendado;
    }

    public function setReagendado(bool $reagendado): self
    {
        $this->reagendado = $reagendado;

        return $this;
    }

    public function getAlunoDiarioPersonal(): ?AlunoDiarioPersonal
    {
        return $this->alunoDiarioPersonal;
    }

    public function setAlunoDiarioPersonal(?AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        $this->alunoDiarioPersonal = $alunoDiarioPersonal;

        // set (or unset) the owning side of the relation if necessary
        if ($alunoDiarioPersonal === null) {
            $newAgendamento_personal = null;
        } else {
            $newAgendamento_personal = $this;
        }

        if ($newAgendamento_personal !== $alunoDiarioPersonal->getAgendamentoPersonal()) {
            $alunoDiarioPersonal->setAgendamentoPersonal($newAgendamento_personal);
        }

        return $this;
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

    public function getSalaFranqueada(): ?SalaFranqueada
    {
        return $this->sala_franqueada;
    }

    public function setSalaFranqueada(?SalaFranqueada $sala_franqueada): self
    {
        $this->sala_franqueada = $sala_franqueada;

        return $this;
    }

    public function getFinalizado(): ?bool
    {
        return $this->finalizado;
    }

    public function setFinalizado(bool $finalizado): self
    {
        $this->finalizado = $finalizado;

        return $this;
    }

    /**
     * @return Collection|DatasReagendamentoPersonal[]
     */
    public function getDatasReagendamentoPersonals(): Collection
    {
        return $this->datasReagendamentoPersonals;
    }

    public function addDatasReagendamentoPersonal(DatasReagendamentoPersonal $datasReagendamentoPersonal): self
    {
        if ($this->datasReagendamentoPersonals->contains($datasReagendamentoPersonal) === false) {
            $this->datasReagendamentoPersonals[] = $datasReagendamentoPersonal;
            $datasReagendamentoPersonal->setAgendamentoPersonal($this);
        }

        return $this;
    }

    public function removeDatasReagendamentoPersonal(DatasReagendamentoPersonal $datasReagendamentoPersonal): self
    {
        if ($this->datasReagendamentoPersonals->contains($datasReagendamentoPersonal) === true) {
            $this->datasReagendamentoPersonals->removeElement($datasReagendamentoPersonal);
            // set the owning side to null (unless already changed)
            if ($datasReagendamentoPersonal->getAgendamentoPersonal() === $this) {
                $datasReagendamentoPersonal->setAgendamentoPersonal(null);
            }
        }

        return $this;
    }


}
