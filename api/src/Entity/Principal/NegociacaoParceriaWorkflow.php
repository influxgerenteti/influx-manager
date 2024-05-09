<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\NegociacaoParceriaWorkflowRepository")
 */
class NegociacaoParceriaWorkflow
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
     * @ORM\Column(type="string", length=1)
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $tipo_workflow;

    /**
     * Se deve mostrar nas opções para alterar a situação
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mostrar_opcoes_situacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Convenio", mappedBy="negociacao_parceria_workflow")
     */
    private $convenios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\EtapasConvenio", mappedBy="negociacao_parceria_workflow")
     */
    private $etapasConvenios;

    public function __construct()
    {
        $this->convenios       = new ArrayCollection();
        $this->etapasConvenios = new ArrayCollection();
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

    public function getTipoWorkflow(): ?string
    {
        return $this->tipo_workflow;
    }

    public function setTipoWorkflow(string $tipo_workflow): self
    {
        $this->tipo_workflow = $tipo_workflow;

        return $this;
    }

    public function getMostrarOpcoesSituacao(): ?bool
    {
        return $this->mostrar_opcoes_situacao;
    }

    public function setMostrarOpcoesSituacao(bool $mostrar_opcoes_situacao): self
    {
        $this->mostrar_opcoes_situacao = $mostrar_opcoes_situacao;

        return $this;
    }


    /**
     * @return Collection|Convenio[]
     */
    public function getConvenios(): Collection
    {
        return $this->convenios;
    }

    public function addConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === false) {
            $this->convenios[] = $convenio;
            $convenio->setNegociacaoParceriaWorkflow($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === true) {
            $this->convenios->removeElement($convenio);
            // set the owning side to null (unless already changed)
            if ($convenio->getNegociacaoParceriaWorkflow() === $this) {
                $convenio->setNegociacaoParceriaWorkflow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EtapasConvenio[]
     */
    public function getEtapasConvenios(): Collection
    {
        return $this->etapasConvenios;
    }

    public function addEtapasConvenio(EtapasConvenio $etapasConvenio): self
    {
        if ($this->etapasConvenios->contains($etapasConvenio) === false) {
            $this->etapasConvenios[] = $etapasConvenio;
            $etapasConvenio->setNegociacaoParceriaWorkflow($this);
        }

        return $this;
    }

    public function removeEtapasConvenio(EtapasConvenio $etapasConvenio): self
    {
        if ($this->etapasConvenios->contains($etapasConvenio) === true) {
            $this->etapasConvenios->removeElement($etapasConvenio);
            // set the owning side to null (unless already changed)
            if ($etapasConvenio->getNegociacaoParceriaWorkflow() === $this) {
                $etapasConvenio->setNegociacaoParceriaWorkflow(null);
            }
        }

        return $this;
    }


}
