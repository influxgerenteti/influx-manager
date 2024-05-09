<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\EtapasConvenioRepository")
 * @ORM\Table(name="etapas_convenio")
 */
class EtapasConvenio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="boolean", options={"default":0, "comment":"Quando for setado com o true (1) indica que a negociação foi peridida e deve-se retirar do fluxo (não é mais tratado)"})
     */
    private $retira_fluxo = false;

    /**
     * @ORM\Column(type="boolean", options={"default":0, "comment":"Quando for setado com o true (1) indica que a negociação foi encerrada de fomra positiva !"})
     */
    private $parceria_firmada = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Convenio", mappedBy="etapas_convenio")
     */
    private $convenios;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\NegociacaoParceriaWorkflow", inversedBy="etapasConvenios")
     */
    private $negociacao_parceria_workflow;

    public function __construct()
    {
        $this->convenios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRetiraFluxo(): ?bool
    {
        return $this->retira_fluxo;
    }

    public function setRetiraFluxo(bool $retira_fluxo): self
    {
        $this->retira_fluxo = $retira_fluxo;

        return $this;
    }

    public function getParceriaFirmada(): ?bool
    {
        return $this->parceria_firmada;
    }

    public function setParceriaFirmada(bool $parceria_firmada): self
    {
        $this->parceria_firmada = $parceria_firmada;

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
            $convenio->setEtapasConvenio($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === true) {
            $this->convenios->removeElement($convenio);
            // set the owning side to null (unless already changed)
            if ($convenio->getEtapasConvenio() === $this) {
                $convenio->setEtapasConvenio(null);
            }
        }

        return $this;
    }

    public function getNegociacaoParceriaWorkflow(): ?NegociacaoParceriaWorkflow
    {
        return $this->negociacao_parceria_workflow;
    }

    public function setNegociacaoParceriaWorkflow(?NegociacaoParceriaWorkflow $negociacao_parceria_workflow): self
    {
        $this->negociacao_parceria_workflow = $negociacao_parceria_workflow;

        return $this;
    }


}
