<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoOcorrenciaRepository")
 */
class TipoOcorrencia
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
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademica", mappedBy="tipo_ocorrencia")
     */
    private $ocorrenciaAcademicas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TipoItem", mappedBy="tipo_ocorrencia")
     */
    private $tipoItems;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"BC - Bônus Classes, IN - Insatisfações, S - Sugestões, O - Outros, F - Falta, AE - Atividade Extra, R - Reposições, A - Avaliações, C - Cobranças, TR - Transferência de Turmas"})
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="TipoOcorrencia")
     * @ORM\JoinColumn(name="tipo_pai", referencedColumnName="id")
     */
    private $tipo_pai;

    /**
     * @ORM\Column(type="integer")
     */
    private $situacao;

    public function __construct()
    {
        $this->ocorrenciaAcademicas = new ArrayCollection();
        $this->tipoItems            = new ArrayCollection();
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

    /**
     * @return Collection|OcorrenciaAcademica[]
     */
    public function getOcorrenciaAcademicas(): Collection
    {
        return $this->ocorrenciaAcademicas;
    }

    public function addOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === false) {
            $this->ocorrenciaAcademicas[] = $ocorrenciaAcademica;
            $ocorrenciaAcademica->setTipoOcorrencia($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === true) {
            $this->ocorrenciaAcademicas->removeElement($ocorrenciaAcademica);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademica->getTipoOcorrencia() === $this) {
                $ocorrenciaAcademica->setTipoOcorrencia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TipoItem[]
     */
    public function getTipoItems(): Collection
    {
        return $this->tipoItems;
    }

    public function addTipoItem(TipoItem $tipoItem): self
    {
        if ($this->tipoItems->contains($tipoItem) === false) {
            $this->tipoItems[] = $tipoItem;
            $tipoItem->setTipoOcorrencia($this);
        }

        return $this;
    }

    public function removeTipoItem(TipoItem $tipoItem): self
    {
        if ($this->tipoItems->contains($tipoItem) === true) {
            $this->tipoItems->removeElement($tipoItem);
            // set the owning side to null (unless already changed)
            if ($tipoItem->getTipoOcorrencia() === $this) {
                $tipoItem->setTipoOcorrencia(null);
            }
        }

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of situacao
     */ 
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * Set the value of situacao
     *
     * @return  self
     */ 
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;

        return $this;
    }

    /**
     * Get the value of tipo_pai
     * 
     * @return TipoOcorrencia
     */ 
    public function getTipoPai(): ?TipoOcorrencia 
    {
        return $this->tipo_pai;
    }

    /**
     * Set the value of tipo_pai
     *
     * @return  self
     */ 
    public function setTipoPai($tipo_pai): self
    {
        $this->tipo_pai = $tipo_pai;

        return $this;
    }
    
}
