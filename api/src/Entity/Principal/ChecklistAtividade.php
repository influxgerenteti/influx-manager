<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ChecklistAtividadeRepository")
 * @ORM\Table(name="checklist_atividade")
 */
class ChecklistAtividade
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
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="string", length=2, options={"default":"D","comment":"(D)iária, (S)emanal, (M)ensal, (A)temporal"})
     */
    private $tipo_atividade = "D";

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Papel", inversedBy="checklistAtividades")
     */
    private $papel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="checklistAtividades")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"(A)tivo, (I)nativo, (R)emovido"})
     */
    private $situacao = "A";

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ChecklistAtividadeRealizada", mappedBy="checklist_atividade")
     */
    private $checklistAtividadeRealizadas;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Checklist", mappedBy="checklist_atividade")
     */
    private $checklists;

    public function __construct()
    {
        $this->papel = new ArrayCollection();
        $this->checklistAtividadeRealizadas = new ArrayCollection();
        $this->checklists   = new ArrayCollection();
        $this->data_criacao = new \DateTime();
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

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getTipoAtividade(): ?string
    {
        return $this->tipo_atividade;
    }

    public function setTipoAtividade(string $tipo_atividade): self
    {
        $this->tipo_atividade = $tipo_atividade;

        return $this;
    }

    /**
     * @return Collection|Papel[]
     */
    public function getPapel(): Collection
    {
        return $this->papel;
    }

    public function addPapel(Papel $papel): self
    {
        if ($this->papel->contains($papel) === false) {
            $this->papel[] = $papel;
        }

        return $this;
    }

    public function removePapel(Papel $papel): self
    {
        if ($this->papel->contains($papel) === true) {
            $this->papel->removeElement($papel);
        }

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
            $checklistAtividadeRealizada->setChecklistAtividade($this);
        }

        return $this;
    }

    public function removeChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === true) {
            $this->checklistAtividadeRealizadas->removeElement($checklistAtividadeRealizada);
            // set the owning side to null (unless already changed)
            if ($checklistAtividadeRealizada->getChecklistAtividade() === $this) {
                $checklistAtividadeRealizada->setChecklistAtividade(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Checklist[]
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklist $checklist): self
    {
        if ($this->checklists->contains($checklist) === false) {
            $this->checklists[] = $checklist;
            $checklist->addChecklistAtividade($this);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): self
    {
        if ($this->checklists->contains($checklist) === true) {
            $this->checklists->removeElement($checklist);
            $checklist->removeChecklistAtividade($this);
        }

        return $this;
    }


}
