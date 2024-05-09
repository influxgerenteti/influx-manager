<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoAgendamentoRepository")
 * @ORM\Table(name="tipo_agendamento")
 */
class TipoAgendamento
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $cor;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $situacao = "A";

    /**
     * @ORM\Column(type="string", length=10, nullable=true, options={"comment"="NP - Negociação de parcerias, ST - Sem Telefone, CT - Com Telefone, APV - Apresentação Pessoal/Visita"})
     */
    private $tipo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="tipo_agendamento")
     */
    private $agendaCompromissos;

    public function __construct()
    {
        $this->agendaCompromissos = new ArrayCollection();
    }

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

    public function getCor(): ?string
    {
        return $this->cor;
    }

    public function setCor(string $cor): self
    {
        $this->cor = $cor;

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return Collection|AgendaCompromisso[]
     */
    public function getAgendaCompromissos(): Collection
    {
        return $this->agendaCompromissos;
    }

    public function addAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === false) {
            $this->agendaCompromissos[] = $agendaCompromisso;
            $agendaCompromisso->setTipoAgendamento($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getTipoAgendamento() === $this) {
                $agendaCompromisso->setTipoAgendamento(null);
            }
        }

        return $this;
    }


}
