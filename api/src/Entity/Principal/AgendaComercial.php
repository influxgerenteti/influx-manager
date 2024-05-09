<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AgendaComercialRepository")
 * @ORM\Table(name="agenda_comercial")
 */
class AgendaComercial
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_agendamento;

    /**
     * @ORM\Column(type="string", length=3, nullable=true, options={"comment": "NC=Nao compareceu, VE=Visita efetiva (1 visita), VE2=Visita efetiva (a partir da 2 visita), VD=Visita desmarcada"})
     */
    private $situacao_visita;

    /**
     * @ORM\Column(type="string", length=3, options={"comment": "C=concluido, NC=Não concluido", "default": "NC"})
     */
    private $situacao = "NC";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoAgendamento")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo_agendamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Interessado", inversedBy="agendaComerciais")
     */
    private $interessado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="agenda_comercial")
     */
    private $followupComercials;

    function __construct()
    {
        $this->data_criacao       = new \DateTime();
        $this->followupComercials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getDataAgendamento(): ?\DateTimeInterface
    {
        return $this->data_agendamento;
    }

    public function setDataAgendamento(\DateTimeInterface $data_agendamento): self
    {
        $this->data_agendamento = $data_agendamento;

        return $this;
    }

    public function getSituacaoVisita(): ?string
    {
        return $this->situacao_visita;
    }

    public function setSituacaoVisita(?string $situacao_visita): self
    {
        $this->situacao_visita = $situacao_visita;

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

    public function getTipoAgendamento(): ?TipoAgendamento
    {
        return $this->tipo_agendamento;
    }

    public function setTipoAgendamento(?TipoAgendamento $tipo_agendamento): self
    {
        $this->tipo_agendamento = $tipo_agendamento;

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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getInteressado(): ?Interessado
    {
        return $this->interessado;
    }

    public function setInteressado(?Interessado $interessado): self
    {
        $this->interessado = $interessado;

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

    /**
     * @return Collection|FollowupComercial[]
     */
    public function getFollowupComercials(): Collection
    {
        return $this->followupComercials;
    }

    public function addFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === false) {
            $this->followupComercials[] = $followupComercial;
            $followupComercial->setAgendaComercial($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getAgendaComercial() === $this) {
                $followupComercial->setAgendaComercial(null);
            }
        }

        return $this;
    }


}
