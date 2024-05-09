<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MotivoNaoFechamentoConvenioRepository")
 * @ORM\Table(name="motivo_nao_fechamento_convenio")
 */
class MotivoNaoFechamentoConvenio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Convenio", mappedBy="motivo_nao_fechamento_convenio")
     */
    private $convenios;

    public function __construct()
    {
        $this->convenios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $convenio->setMotivoNaoFechamentoConvenio($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === true) {
            $this->convenios->removeElement($convenio);
            // set the owning side to null (unless already changed)
            if ($convenio->getMotivoNaoFechamentoConvenio() === $this) {
                $convenio->setMotivoNaoFechamentoConvenio(null);
            }
        }

        return $this;
    }


}
