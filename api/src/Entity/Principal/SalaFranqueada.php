<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\SalaFranqueadaRepository")
 * @ORM\Table(name="sala_franqueada")
 */
class SalaFranqueada
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lotacao_maxima;

    /**
     * @ORM\Column(type="boolean")
     */
    private $personal;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = "A";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Sala", inversedBy="salaFranqueadas", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="sala_franqueada")
     */
    private $alunoDiarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="sala_franqueada")
     */
    private $atividadeExtras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="sala_franqueada")
     */
    private $reposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendamentoPersonal", mappedBy="sala_franqueada")
     */
    private $agendamentoPersonals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\BonusClass", mappedBy="sala_franqueada")
     */
    private $bonusClasses;

    public function __construct()
    {
        $this->salaFranqueadas      = new ArrayCollection();
        $this->alunoDiarios         = new ArrayCollection();
        $this->atividadeExtras      = new ArrayCollection();
        $this->reposicaoAulas       = new ArrayCollection();
        $this->agendamentoPersonals = new ArrayCollection();
        $this->bonusClasses         = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLotacaoMaxima(): ?int
    {
        return $this->lotacao_maxima;
    }

    public function setLotacaoMaxima(?int $lotacao_maxima): self
    {
        $this->lotacao_maxima = $lotacao_maxima;

        return $this;
    }

    public function getPersonal(): ?bool
    {
        return $this->personal;
    }

    public function setPersonal(bool $personal): self
    {
        $this->personal = $personal;

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

    public function getSala(): ?Sala
    {
        return $this->sala;
    }

    public function setSala(?Sala $sala): self
    {
        $this->sala = $sala;

        return $this;
    }

    /**
     * @return Collection|AlunoDiario[]
     */
    public function getAlunoDiarios(): Collection
    {
        return $this->alunoDiarios;
    }

    public function addAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === false) {
            $this->alunoDiarios[] = $alunoDiario;
            $alunoDiario->setSalaFranqueada($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getSalaFranqueada() === $this) {
                $alunoDiario->setSalaFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AtividadeExtra[]
     */
    public function getAtividadeExtras(): Collection
    {
        return $this->atividadeExtras;
    }

    public function addAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === false) {
            $this->atividadeExtras[] = $atividadeExtra;
            $atividadeExtra->setSalaFranqueada($this);
        }

        return $this;
    }

    public function removeAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === true) {
            $this->atividadeExtras->removeElement($atividadeExtra);
            // set the owning side to null (unless already changed)
            if ($atividadeExtra->getSalaFranqueada() === $this) {
                $atividadeExtra->setSalaFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulas(): Collection
    {
        return $this->reposicaoAulas;
    }

    public function addReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === false) {
            $this->reposicaoAulas[] = $reposicaoAula;
            $reposicaoAula->setSalaFranqueada($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getSalaFranqueada() === $this) {
                $reposicaoAula->setSalaFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AgendamentoPersonal[]
     */
    public function getAgendamentoPersonals(): Collection
    {
        return $this->agendamentoPersonals;
    }

    public function addAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === false) {
            $this->agendamentoPersonals[] = $agendamentoPersonal;
            $agendamentoPersonal->setSalaFranqueada($this);
        }

        return $this;
    }

    public function removeAgendamentoPersonal(AgendamentoPersonal $agendamentoPersonal): self
    {
        if ($this->agendamentoPersonals->contains($agendamentoPersonal) === true) {
            $this->agendamentoPersonals->removeElement($agendamentoPersonal);
            // set the owning side to null (unless already changed)
            if ($agendamentoPersonal->getSalaFranqueada() === $this) {
                $agendamentoPersonal->setSalaFranqueada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BonusClass[]
     */
    public function getBonusClasses(): Collection
    {
        return $this->bonusClasses;
    }

    public function addBonusClass(BonusClass $bonusClass): self
    {
        if ($this->bonusClasses->contains($bonusClass) === false) {
            $this->bonusClasses[] = $bonusClass;
            $bonusClass->setSalaFranqueada($this);
        }

        return $this;
    }

    public function removeBonusClass(BonusClass $bonusClass): self
    {
        if ($this->bonusClasses->contains($bonusClass) === true) {
            $this->bonusClasses->removeElement($bonusClass);
            // set the owning side to null (unless already changed)
            if ($bonusClass->getSalaFranqueada() === $this) {
                $bonusClass->setSalaFranqueada(null);
            }
        }

        return $this;
    }


}
