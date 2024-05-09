<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ChecklistRepository")
 */
class Checklist
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
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"(A)tivo, (I)nativo, (R)emovido"})
     */
    private $situacao = "A";

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\ChecklistAtividade", inversedBy="checklists")
     */
    private $checklist_atividade;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ChecklistAtividadeRealizada", mappedBy="checklist")
     */
    private $checklistAtividadeRealizadas;

    public function __construct()
    {
        $this->checklist_atividade          = new ArrayCollection();
        $this->checklistAtividadeRealizadas = new ArrayCollection();
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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    /**
     * @return Collection|ChecklistAtividade[]
     */
    public function getChecklistAtividade(): Collection
    {
        return $this->checklist_atividade;
    }

    public function addChecklistAtividade(ChecklistAtividade $checklistAtividade): self
    {
        if ($this->checklist_atividade->contains($checklistAtividade) === false) {
            $this->checklist_atividade[] = $checklistAtividade;
        }

        return $this;
    }

    public function removeChecklistAtividade(ChecklistAtividade $checklistAtividade): self
    {
        if ($this->checklist_atividade->contains($checklistAtividade) === true) {
            $this->checklist_atividade->removeElement($checklistAtividade);
        }

        return $this;
    }

    /**
     * @return Collection|ChecklistAtividadeRealizada[]
     */
    public function getChecklistAtividadeRealizadas(): Collection
    {
        return $this->checklistAtividadeRealizadas;
    }

    public function addChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === false) {
            $this->checklistAtividadeRealizadas[] = $checklistAtividadeRealizada;
            $checklistAtividadeRealizada->setChecklist($this);
        }

        return $this;
    }

    public function removeChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === true) {
            $this->checklistAtividadeRealizadas->removeElement($checklistAtividadeRealizada);
            // set the owning side to null (unless already changed)
            if ($checklistAtividadeRealizada->getChecklist() === $this) {
                $checklistAtividadeRealizada->setChecklist(null);
            }
        }

        return $this;
    }


}
