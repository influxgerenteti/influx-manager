<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoContatoRepository")
 * @ORM\Table(name="tipo_contato")
 */
class TipoContato
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
    private $nome;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="tipo_contato")
     */
    private $followupComercials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupConvenio", mappedBy="tipo_contato")
     */
    private $followupConvenios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="tipo_contato")
     */
    private $interessados;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $tipo;

    public function __construct()
    {
        $this->followupComercials = new ArrayCollection();
        $this->followupConvenios  = new ArrayCollection();
        $this->interessados       = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

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
            $followupComercial->setTipoContato($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getTipoContato() === $this) {
                $followupComercial->setTipoContato(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FollowupConvenio[]
     */
    public function getFollowupConvenios(): Collection
    {
        return $this->followupConvenios;
    }

    public function addFollowupConvenio(FollowupConvenio $followupConvenio): self
    {
        if ($this->followupConvenios->contains($followupConvenio) === false) {
            $this->followupConvenios[] = $followupConvenio;
            $followupConvenio->setTipoContato($this);
        }

        return $this;
    }

    public function removeFollowupConvenio(FollowupConvenio $followupConvenio): self
    {
        if ($this->followupConvenios->contains($followupConvenio) === true) {
            $this->followupConvenios->removeElement($followupConvenio);
            // set the owning side to null (unless already changed)
            if ($followupConvenio->getTipoContato() === $this) {
                $followupConvenio->setTipoContato(null);
            }
        }

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
            $interessado->setTipoContato($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            // set the owning side to null (unless already changed)
            if ($interessado->getTipoContato() === $this) {
                $interessado->setTipoContato(null);
            }
        }

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


}
