<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ChecklistAtividadeRealizadaRepository")
 * @ORM\Table(name="checklist_atividade_realizada")
 */
class ChecklistAtividadeRealizada
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="checklistAtividadeRealizadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ChecklistAtividade", inversedBy="checklistAtividadeRealizadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checklist_atividade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="checklistAtividadeRealizadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_conclusao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Checklist", inversedBy="checklistAtividadeRealizadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checklist;

    public function __construct()
    {
        $this->data_conclusao = new \DateTime();
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

    public function getChecklistAtividade(): ?ChecklistAtividade
    {
        return $this->checklist_atividade;
    }

    public function setChecklistAtividade(?ChecklistAtividade $checklist_atividade): self
    {
        $this->checklist_atividade = $checklist_atividade;

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

    public function getDataConclusao(): ?\DateTimeInterface
    {
        return $this->data_conclusao;
    }

    public function setDataConclusao(\DateTimeInterface $data_conclusao): self
    {
        $this->data_conclusao = $data_conclusao;

        return $this;
    }

    public function getChecklist(): ?Checklist
    {
        return $this->checklist;
    }

    public function setChecklist(?Checklist $checklist): self
    {
        $this->checklist = $checklist;

        return $this;
    }


}
