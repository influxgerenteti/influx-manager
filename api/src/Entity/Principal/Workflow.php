<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\WorkflowRepository")
 */
class Workflow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $situacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="workflow")
     */
    private $interessados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\WorkflowAcao", mappedBy="workflow")
     */
    private $workflowAcaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\WorkflowAcao", mappedBy="destino_workflow")
     */
    private $workflowAcaoPossuiWorkflowComoDestino;

    /**
     * @ORM\Column(type="string", length=10, options={"comment":"WCI - Contato Inicial, WRTFL - Retorno Telefonico"})
     */
    private $tipo_workflow;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="workflow")
     */
    private $followupComercials;

    public function __construct()
    {
        $this->interessados  = new ArrayCollection();
        $this->workflowAcaos = new ArrayCollection();
        $this->workflowAcaoPossuiWorkflowComoDestino = new ArrayCollection();
        $this->followupComercials = new ArrayCollection();
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
            $interessado->setWorkflow($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            // set the owning side to null (unless already changed)
            if ($interessado->getWorkflow() === $this) {
                $interessado->setWorkflow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkflowAcao[]
     */
    public function getWorkflowAcaos(): Collection
    {
        return $this->workflowAcaos;
    }

    public function addWorkflowAcao(WorkflowAcao $workflowAcao): self
    {
        if ($this->workflowAcaos->contains($workflowAcao) === false) {
            $this->workflowAcaos[] = $workflowAcao;
            $workflowAcao->setWorkflow($this);
        }

        return $this;
    }

    public function removeWorkflowAcao(WorkflowAcao $workflowAcao): self
    {
        if ($this->workflowAcaos->contains($workflowAcao) === true) {
            $this->workflowAcaos->removeElement($workflowAcao);
            // set the owning side to null (unless already changed)
            if ($workflowAcao->getWorkflow() === $this) {
                $workflowAcao->setWorkflow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WorkflowAcao[]
     */
    public function getWorkflowAcaoPossuiWorkflowComoDestino(): Collection
    {
        return $this->workflowAcaoPossuiWorkflowComoDestino;
    }

    public function addWorkflowAcaoPossuiWorkflowComoDestino(WorkflowAcao $workflowAcaoPossuiWorkflowComoDestino): self
    {
        if ($this->workflowAcaoPossuiWorkflowComoDestino->contains($workflowAcaoPossuiWorkflowComoDestino) === false) {
            $this->workflowAcaoPossuiWorkflowComoDestino[] = $workflowAcaoPossuiWorkflowComoDestino;
            $workflowAcaoPossuiWorkflowComoDestino->setDestinoWorkflow($this);
        }

        return $this;
    }

    public function removeWorkflowAcaoPossuiWorkflowComoDestino(WorkflowAcao $workflowAcaoPossuiWorkflowComoDestino): self
    {
        if ($this->workflowAcaoPossuiWorkflowComoDestino->contains($workflowAcaoPossuiWorkflowComoDestino) === true) {
            $this->workflowAcaoPossuiWorkflowComoDestino->removeElement($workflowAcaoPossuiWorkflowComoDestino);
            // set the owning side to null (unless already changed)
            if ($workflowAcaoPossuiWorkflowComoDestino->getDestinoWorkflow() === $this) {
                $workflowAcaoPossuiWorkflowComoDestino->setDestinoWorkflow(null);
            }
        }

        return $this;
    }

    public function getTipoWorkflow(): ?string
    {
        return $this->tipo_workflow;
    }

    public function setTipoWorkflow(string $tipo_workflow): self
    {
        $this->tipo_workflow = $tipo_workflow;

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
            $followupComercial->setWorkflow($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getWorkflow() === $this) {
                $followupComercial->setWorkflow(null);
            }
        }

        return $this;
    }


}
