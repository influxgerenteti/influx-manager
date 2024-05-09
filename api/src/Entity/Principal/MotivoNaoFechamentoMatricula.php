<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MotivoNaoFechamentoMatriculaRepository")
 * @ORM\Table(name="motivo_nao_fechamento_matricula")
 */
class MotivoNaoFechamentoMatricula
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
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = "A";

    /**
     * @ORM\Column(type="boolean", options={"comment":"Efetivo são os motivos que caracterizam o workflow gerado como efetivo do ponto de vista comercial - definido pelo Paulex"})
     */
    private $efetivo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="motivo_nao_fechamento")
     */
    private $interessados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="motivo_nao_fechamento")
     */
    private $followupComercials;

    public function __construct()
    {
        $this->interessados       = new ArrayCollection();
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
            $interessado->setMotivoNaoFechamento($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            // set the owning side to null (unless already changed)
            if ($interessado->getMotivoNaoFechamento() === $this) {
                $interessado->setMotivoNaoFechamento(null);
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
            $followupComercial->setMotivoNaoFechamento($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getMotivoNaoFechamento() === $this) {
                $followupComercial->setMotivoNaoFechamento(null);
            }
        }

        return $this;
    }


}
