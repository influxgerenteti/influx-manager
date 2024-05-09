<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\WorkflowAcaoRepository")
 * @ORM\Table(name="workflow_acao")
 */
class WorkflowAcao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Workflow", inversedBy="workflowAcaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $workflow;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Workflow", inversedBy="workflowAcaoPossuiWorkflowComoDestino")
     */
    private $destino_workflow;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="boolean", options={"comment":"Quando for setado como verdadeiro indica que a negociação foi perdida e deve-se retirar do fluxo (não é mais tratado)"})
     */
    private $retira_fluxo = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="workflow_acao")
     */
    private $interessados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="workflow_acao")
     */
    private $followupComercials;

    /**
     * @ORM\Column(type="boolean", options={"comment":"Efetivo são as ações consideradas efetivas do ponto de vista comercial - definido pelo Paulex"})
     */
    private $efetivo;

    public function __construct()
    {
        $this->interessados       = new ArrayCollection();
        $this->followupComercials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkflow(): ?Workflow
    {
        return $this->workflow;
    }

    public function setWorkflow(?Workflow $workflow): self
    {
        $this->workflow = $workflow;

        return $this;
    }

    public function getDestinoWorkflow(): ?Workflow
    {
        return $this->destino_workflow;
    }

    public function setDestinoWorkflow(?Workflow $destino_workflow): self
    {
        $this->destino_workflow = $destino_workflow;

        return $this;
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

    public function getRetiraFluxo(): ?bool
    {
        return $this->retira_fluxo;
    }

    public function setRetiraFluxo(bool $retira_fluxo): self
    {
        $this->retira_fluxo = $retira_fluxo;

        return $this;
    }

    public function getEfetivo(): ?bool
    {
        return $this->efetivo;
    }

    public function setEfetivo(bool $efetivo): self
    {
        $this->efetivo = $efetivo;

        return $this;
    }

    /**
     * @return Collection|Interessado[]
     */
    public function getInteressados(): Collection
    {
        return $this->interessados;
    }

    public function addInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === false) {
            $this->interessados[] = $interessado;
            $interessado->setWorkflowAcao($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            // set the owning side to null (unless already changed)
            if ($interessado->getWorkflowAcao() === $this) {
                $interessado->setWorkflowAcao(null);
            }
        }

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
            $followupComercial->setWorkflowAcao($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getWorkflowAcao() === $this) {
                $followupComercial->setWorkflowAcao(null);
            }
        }

        return $this;
    }


}
