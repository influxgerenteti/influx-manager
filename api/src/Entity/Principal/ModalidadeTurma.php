<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ModalidadeTurmaRepository")
 * @ORM\Table(name="modalidade_turma")
 */
class ModalidadeTurma
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_modality_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_modality_reference_code;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="modalidade_turma")
     */
    private $contratos;

    public function __construct()
    {
        $this->contratos = new ArrayCollection();
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    public function addContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === false) {
            $this->contratos[] = $contrato;
            $contrato->setModalidadeTurma($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getModalidadeTurma() === $this) {
                $contrato->setModalidadeTurma(null);
            }
        }

        return $this;
    }



    /**
     * Get the value of portal_modality_id
     */
    public function getPortal_modality_id()
    {
        return $this->portal_modality_id;
    }

    /**
     * Set the value of portal_modality_id
     *
     * @return self
     */
    public function setPortal_modality_id($portal_modality_id)
    {
        $this->portal_modality_id = $portal_modality_id;

        return $this;
    }

    /**
     * Get the value of portal_modality_reference_code
     */
    public function getPortal_modality_reference_code()
    {
        return $this->portal_modality_reference_code;
    }

    /**
     * Set the value of portal_modality_reference_code
     *
     * @return self
     */
    public function setPortal_modality_reference_code($portal_modality_reference_code)
    {
        $this->portal_modality_reference_code = $portal_modality_reference_code;

        return $this;
    }


}
