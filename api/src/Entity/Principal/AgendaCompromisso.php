<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AgendaCompromissoRepository")
 */
class AgendaCompromisso
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="agendaCompromissos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoAgendamento", inversedBy="agendaCompromissos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo_agendamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="agendaCompromissos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="agendaCompromissos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AtividadeExtra", inversedBy="agendaCompromissos")
     */
    private $atividade_extra;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\OcorrenciaAcademica", inversedBy="agendaCompromissos")
     */
    private $ocorrencia_academica;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_hora_inicio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_hora_fim;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="boolean")
     */
    private $privado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AgendaCompromisso", inversedBy="agendaCompromissos")
     * @ORM\JoinColumn(name="periodo_pai_id",                                referencedColumnName="id", onDelete="SET NULL")
     */
    private $periodo_pai;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="periodo_pai")
     */
    private $agendaCompromissos;

    function __construct()
    {
        $this->data_criacao       = new \DateTime();
        $this->data_hora_fim      = null;
        $this->privado            = false;
        $this->agendaCompromissos = new ArrayCollection();
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

    public function getTipoAgendamento(): ?TipoAgendamento
    {
        return $this->tipo_agendamento;
    }

    public function setTipoAgendamento(?TipoAgendamento $tipo_agendamento): self
    {
        $this->tipo_agendamento = $tipo_agendamento;

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

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

        return $this;
    }

    public function getAtividadeExtra(): ?AtividadeExtra
    {
        return $this->atividade_extra;
    }

    public function setAtividadeExtra(?AtividadeExtra $atividade_extra): self
    {
        $this->atividade_extra = $atividade_extra;

        return $this;
    }

    public function getOcorrenciaAcademica(): ?OcorrenciaAcademica
    {
        return $this->ocorrencia_academica;
    }

    public function setOcorrenciaAcademica(?OcorrenciaAcademica $ocorrencia_academica): self
    {
        $this->ocorrencia_academica = $ocorrencia_academica;

        return $this;
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

    public function getDataHoraInicio(): ?\DateTimeInterface
    {
        return $this->data_hora_inicio;
    }

    public function setDataHoraInicio(\DateTimeInterface $data_hora_inicio): self
    {
        $this->data_hora_inicio = $data_hora_inicio;

        return $this;
    }

    public function getDataHoraFim(): ?\DateTimeInterface
    {
        return $this->data_hora_fim;
    }

    public function setDataHoraFim(?\DateTimeInterface $data_hora_fim): self
    {
        $this->data_hora_fim = $data_hora_fim;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getPrivado(): ?bool
    {
        return $this->privado;
    }

    public function setPrivado(bool $privado): self
    {
        $this->privado = $privado;

        return $this;
    }

    public function getPeriodoPai(): ?self
    {
        return $this->periodo_pai;
    }

    public function setPeriodoPai(?self $periodo_pai): self
    {
        $this->periodo_pai = $periodo_pai;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAgendaCompromissos(): Collection
    {
        return $this->agendaCompromissos;
    }

    public function addAgendaCompromisso(self $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === false) {
            $this->agendaCompromissos[] = $agendaCompromisso;
            $agendaCompromisso->setPeriodoPai($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(self $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getPeriodoPai() === $this) {
                $agendaCompromisso->setPeriodoPai(null);
            }
        }

        return $this;
    }


}
