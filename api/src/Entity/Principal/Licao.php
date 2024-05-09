<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\LicaoRepository")
 */
class Licao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"A - ATIVO, I - INATIVO","default":"A"})
     */
    private $situacao = 'A';

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"P - Plataforma, V - Ao vivo","default":"V"})
     */
    private $modalidade = 'V';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanejamentoLicao", inversedBy="licaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planejamento_licao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TurmaAula", mappedBy="licao")
     */
    private $turmaAulas;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="licao")
     */
    private $alunoDiarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="licao")
     */
    private $reposicaoAulas;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\AlunoDiarioPersonal", mappedBy="aluno_diario_personal_licao")
     */
    private $alunoDiarioPersonals;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hybrid_id;

    public function __construct()
    {
        $this->turmaAulas           = new ArrayCollection();
        $this->alunoDiarios         = new ArrayCollection();
        $this->reposicaoAulas       = new ArrayCollection();
        $this->alunoDiarioPersonals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

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

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

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

    public function getModalidade(): ?string
    {
        return $this->modalidade;
    }

    public function setModalidade(string $modalidade): self
    {
        $this->modalidade = $modalidade;

        return $this;
    }

    public function getPlanejamentoLicao(): ?PlanejamentoLicao
    {
        return $this->planejamento_licao;
    }

    public function setPlanejamentoLicao(?PlanejamentoLicao $planejamento_licao): self
    {
        $this->planejamento_licao = $planejamento_licao;

        return $this;
    }

    /**
     * @return Collection|TurmaAula[]
     */
    public function getTurmaAulas(): Collection
    {
        return $this->turmaAulas;
    }

    public function addTurmaAula(TurmaAula $turmaAula): self
    {
        if ($this->turmaAulas->contains($turmaAula) === false) {
            $this->turmaAulas[] = $turmaAula;
            $turmaAula->setLicao($this);
        }

        return $this;
    }

    public function removeTurmaAula(TurmaAula $turmaAula): self
    {
        if ($this->turmaAulas->contains($turmaAula) === true) {
            $this->turmaAulas->removeElement($turmaAula);
            // set the owning side to null (unless already changed)
            if ($turmaAula->getLicao() === $this) {
                $turmaAula->setLicao(null);
            }
        }

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
            $alunoDiario->addLicao($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            $alunoDiario->removeLicao($this);
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
            $reposicaoAula->setLicao($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getLicao() === $this) {
                $reposicaoAula->setLicao(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoDiarioPersonal[]
     */
    public function getAlunoDiarioPersonals(): Collection
    {
        return $this->alunoDiarioPersonals;
    }

    public function addAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === false) {
            $this->alunoDiarioPersonals[] = $alunoDiarioPersonal;
            $alunoDiarioPersonal->addAlunoDiarioPersonalLicao($this);
        }

        return $this;
    }

    public function removeAlunoDiarioPersonal(AlunoDiarioPersonal $alunoDiarioPersonal): self
    {
        if ($this->alunoDiarioPersonals->contains($alunoDiarioPersonal) === true) {
            $this->alunoDiarioPersonals->removeElement($alunoDiarioPersonal);
            $alunoDiarioPersonal->removeAlunoDiarioPersonalLicao($this);
        }

        return $this;
    }



    /**
     * Get the value of hybrid_id
     */
    public function getHybrid_id()
    {
        return $this->hybrid_id;
    }

    /**
     * Set the value of hybrid_id
     *
     * @return self
     */
    public function setHybrid_id($hybrid_id)
    {
        $this->hybrid_id = $hybrid_id;

        return $this;
    }


}
