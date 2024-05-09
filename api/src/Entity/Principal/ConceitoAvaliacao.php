<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ConceitoAvaliacaoRepository")
 * @ORM\Table(name="conceito_avaliacao")
 */
class ConceitoAvaliacao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $descricao;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="nota_mid_term_oral")
     */
    private $reposicaoAulasNmto;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="nota_final_oral")
     */
    private $reposicaoAulasNfo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="nota_retake_mid_term_oral")
     */
    private $reposicaoAulasNrmto;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="nota_retake_final_oral")
     */
    private $reposicaoAulasNrfo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="mid_term_oral_anterior")
     */
    private $historicoReposicaoAulasMtoa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="mid_term_oral_atual")
     */
    private $historicoReposicaoAulasMtoat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="nota_mid_term_oral")
     */
    private $alunoAvaliacaosNmto;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="nota_final_oral")
     */
    private $alunoAvalicaosNfo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="nota_retake_mid_term_oral")
     */
    private $alunoAvaliacaosNrmto;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="nota_retake_final_oral")
     */
    private $alunoAvaliacaosNrfo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="final_oral_anterior")
     */
    private $historicoReposicaoAulasFoa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="retake_mid_term_oral_anterior")
     */
    private $historicoReposicaoAulasRmtoa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="retake_mid_term_oral_atual")
     */
    private $historicoReposicaoAulasRmtoat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="retake_final_oral_anterior")
     */
    private $historicoReposicaoAulasRfoa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="retake_final_oral_atual")
     */
    private $historicoReposicaoAulasRfoat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoReposicaoAula", mappedBy="final_oral_atual")
     */
    private $historicoReposicaoAulasFoat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="nota_listening_1")
     */
    private $alunoAvaliacaoConceituals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="nota_speaking_1")
     */
    private $alunoAvaliacaoConceitualsNs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="nota_writing_1")
     */
    private $alunoAvaliacaoConceitualsNw;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="nota_listening_2")
     */
    private $alunoAvaliacaoConceitualsNl2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="nota_speaking_2")
     */
    private $alunoAvaliacaoConceitualsNs2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="nota_writing_2")
     */
    private $alunoAvaliacaoConceitualsNw2;

    public function __construct()
    {
        $this->reposicaoAulasNmto           = new ArrayCollection();
        $this->reposicaoAulasNfo            = new ArrayCollection();
        $this->reposicaoAulasNrmto          = new ArrayCollection();
        $this->reposicaoAulasNrfo           = new ArrayCollection();
        $this->historicoReposicaoAulasMtoa  = new ArrayCollection();
        $this->historicoReposicaoAulasMtoat = new ArrayCollection();
        $this->alunoAvaliacaosNmto          = new ArrayCollection();
        $this->alunoAvalicaosNfo            = new ArrayCollection();
        $this->alunoAvaliacaosNrmto         = new ArrayCollection();
        $this->alunoAvaliacaosNrfo          = new ArrayCollection();
        $this->historicoReposicaoAulasFoa   = new ArrayCollection();
        $this->historicoReposicaoAulasRmtoa = new ArrayCollection();
        $this->historicoReposicaoAulasRmtoat = new ArrayCollection();
        $this->historicoReposicaoAulasRfoa   = new ArrayCollection();
        $this->historicoReposicaoAulasRfoat  = new ArrayCollection();
        $this->historicoReposicaoAulasFoat   = new ArrayCollection();
        $this->alunoAvaliacaoConceituals     = new ArrayCollection();
        $this->alunoAvaliacaoConceitualsNs   = new ArrayCollection();
        $this->alunoAvaliacaoConceitualsNw   = new ArrayCollection();
        $this->alunoAvaliacaoConceitualsNl2  = new ArrayCollection();
        $this->alunoAvaliacaoConceitualsNs2  = new ArrayCollection();
        $this->alunoAvaliacaoConceitualsNw2  = new ArrayCollection();
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

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulasNmto(): Collection
    {
        return $this->reposicaoAulasNmto;
    }

    public function addReposicaoAulaNmto(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulasNmto->contains($reposicaoAula) === false) {
            $this->reposicaoAulasNmto[] = $reposicaoAula;
            $reposicaoAula->setNotaMidTermOral($this);
        }

        return $this;
    }

    public function removeReposicaoAulaNmto(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulasNmto->contains($reposicaoAula) === true) {
            $this->reposicaoAulasNmto->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getNotaMidTermOral() === $this) {
                $reposicaoAula->setNotaMidTermOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulasNfo(): Collection
    {
        return $this->reposicaoAulasNfo;
    }

    public function addReposicaoAulasNfo(ReposicaoAula $reposicaoAulasNfo): self
    {
        if ($this->reposicaoAulasNfo->contains($reposicaoAulasNfo) === false) {
            $this->reposicaoAulasNfo[] = $reposicaoAulasNfo;
            $reposicaoAulasNfo->setNotaFinalOral($this);
        }

        return $this;
    }

    public function removeReposicaoAulasNfo(ReposicaoAula $reposicaoAulasNfo): self
    {
        if ($this->reposicaoAulasNfo->contains($reposicaoAulasNfo) === true) {
            $this->reposicaoAulasNfo->removeElement($reposicaoAulasNfo);
            // set the owning side to null (unless already changed)
            if ($reposicaoAulasNfo->getNotaFinalOral() === $this) {
                $reposicaoAulasNfo->setNotaFinalOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulasNrmto(): Collection
    {
        return $this->reposicaoAulasNrmto;
    }

    public function addReposicaoAulasNrmto(ReposicaoAula $reposicaoAulasNrmto): self
    {
        if ($this->reposicaoAulasNrmto->contains($reposicaoAulasNrmto) === false) {
            $this->reposicaoAulasNrmto[] = $reposicaoAulasNrmto;
            $reposicaoAulasNrmto->setNotaRetakeMidTermOral($this);
        }

        return $this;
    }

    public function removeReposicaoAulasNrmto(ReposicaoAula $reposicaoAulasNrmto): self
    {
        if ($this->reposicaoAulasNrmto->contains($reposicaoAulasNrmto) === true) {
            $this->reposicaoAulasNrmto->removeElement($reposicaoAulasNrmto);
            // set the owning side to null (unless already changed)
            if ($reposicaoAulasNrmto->getNotaRetakeMidTermOral() === $this) {
                $reposicaoAulasNrmto->setNotaRetakeMidTermOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulasNrfo(): Collection
    {
        return $this->reposicaoAulasNrfo;
    }

    public function addReposicaoAulasNrfo(ReposicaoAula $reposicaoAulasNrfo): self
    {
        if ($this->reposicaoAulasNrfo->contains($reposicaoAulasNrfo) === false) {
            $this->reposicaoAulasNrfo[] = $reposicaoAulasNrfo;
            $reposicaoAulasNrfo->setNotaRetakeFinalOral($this);
        }

        return $this;
    }

    public function removeReposicaoAulasNrfo(ReposicaoAula $reposicaoAulasNrfo): self
    {
        if ($this->reposicaoAulasNrfo->contains($reposicaoAulasNrfo) === true) {
            $this->reposicaoAulasNrfo->removeElement($reposicaoAulasNrfo);
            // set the owning side to null (unless already changed)
            if ($reposicaoAulasNrfo->getNotaRetakeFinalOral() === $this) {
                $reposicaoAulasNrfo->setNotaRetakeFinalOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasMtoa(): Collection
    {
        return $this->historicoReposicaoAulasMtoa;
    }

    public function addHistoricoReposicaoAulasMtoa(HistoricoReposicaoAula $historicoReposicaoAulasMtoa): self
    {
        if ($this->historicoReposicaoAulasMtoa->contains($historicoReposicaoAulasMtoa) === false) {
            $this->historicoReposicaoAulasMtoa[] = $historicoReposicaoAulasMtoa;
            $historicoReposicaoAulasMtoa->setMidTermOralAnterior($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasMtoa(HistoricoReposicaoAula $historicoReposicaoAulasMtoa): self
    {
        if ($this->historicoReposicaoAulasMtoa->contains($historicoReposicaoAulasMtoa) === true) {
            $this->historicoReposicaoAulasMtoa->removeElement($historicoReposicaoAulasMtoa);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasMtoa->getMidTermOralAnterior() === $this) {
                $historicoReposicaoAulasMtoa->setMidTermOralAnterior(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasMtoat(): Collection
    {
        return $this->historicoReposicaoAulasMtoat;
    }

    public function addHistoricoReposicaoAulasMtoat(HistoricoReposicaoAula $historicoReposicaoAulasMtoat): self
    {
        if ($this->historicoReposicaoAulasMtoat->contains($historicoReposicaoAulasMtoat) === false) {
            $this->historicoReposicaoAulasMtoat[] = $historicoReposicaoAulasMtoat;
            $historicoReposicaoAulasMtoat->setMidTermOralAtual($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasMtoat(HistoricoReposicaoAula $historicoReposicaoAulasMtoat): self
    {
        if ($this->historicoReposicaoAulasMtoat->contains($historicoReposicaoAulasMtoat) === true) {
            $this->historicoReposicaoAulasMtoat->removeElement($historicoReposicaoAulasMtoat);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasMtoat->getMidTermOralAtual() === $this) {
                $historicoReposicaoAulasMtoat->setMidTermOralAtual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacao[]
     */
    public function getAlunoAvaliacaosNmto(): Collection
    {
        return $this->alunoAvaliacaosNmto;
    }

    public function addAlunoAvaliacaosNmto(AlunoAvaliacao $alunoAvaliacaosNmto): self
    {
        if ($this->alunoAvaliacaosNmto->contains($alunoAvaliacaosNmto) === false) {
            $this->alunoAvaliacaosNmto[] = $alunoAvaliacaosNmto;
            $alunoAvaliacaosNmto->setNotaMidTermOral($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaosNmto(AlunoAvaliacao $alunoAvaliacaosNmto): self
    {
        if ($this->alunoAvaliacaosNmto->contains($alunoAvaliacaosNmto) === true) {
            $this->alunoAvaliacaosNmto->removeElement($alunoAvaliacaosNmto);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaosNmto->getNotaMidTermOral() === $this) {
                $alunoAvaliacaosNmto->setNotaMidTermOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacao[]
     */
    public function getAlunoAvalicaosNfo(): Collection
    {
        return $this->alunoAvalicaosNfo;
    }

    public function addAlunoAvalicaosNfo(AlunoAvaliacao $alunoAvalicaosNfo): self
    {
        if ($this->alunoAvalicaosNfo->contains($alunoAvalicaosNfo) === false) {
            $this->alunoAvalicaosNfo[] = $alunoAvalicaosNfo;
            $alunoAvalicaosNfo->setNotaFinalOral($this);
        }

        return $this;
    }

    public function removeAlunoAvalicaosNfo(AlunoAvaliacao $alunoAvalicaosNfo): self
    {
        if ($this->alunoAvalicaosNfo->contains($alunoAvalicaosNfo) === true) {
            $this->alunoAvalicaosNfo->removeElement($alunoAvalicaosNfo);
            // set the owning side to null (unless already changed)
            if ($alunoAvalicaosNfo->getNotaFinalOral() === $this) {
                $alunoAvalicaosNfo->setNotaFinalOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacao[]
     */
    public function getAlunoAvaliacaosNrmto(): Collection
    {
        return $this->alunoAvaliacaosNrmto;
    }

    public function addAlunoAvaliacaosNrmto(AlunoAvaliacao $alunoAvaliacaosNrmto): self
    {
        if ($this->alunoAvaliacaosNrmto->contains($alunoAvaliacaosNrmto) === false) {
            $this->alunoAvaliacaosNrmto[] = $alunoAvaliacaosNrmto;
            $alunoAvaliacaosNrmto->setNotaRetakeMidTermOral($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaosNrmto(AlunoAvaliacao $alunoAvaliacaosNrmto): self
    {
        if ($this->alunoAvaliacaosNrmto->contains($alunoAvaliacaosNrmto) === true) {
            $this->alunoAvaliacaosNrmto->removeElement($alunoAvaliacaosNrmto);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaosNrmto->getNotaRetakeMidTermOral() === $this) {
                $alunoAvaliacaosNrmto->setNotaRetakeMidTermOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacao[]
     */
    public function getAlunoAvaliacaosNrfo(): Collection
    {
        return $this->alunoAvaliacaosNrfo;
    }

    public function addAlunoAvaliacaosNrfo(AlunoAvaliacao $alunoAvaliacaosNrfo): self
    {
        if ($this->alunoAvaliacaosNrfo->contains($alunoAvaliacaosNrfo) === false) {
            $this->alunoAvaliacaosNrfo[] = $alunoAvaliacaosNrfo;
            $alunoAvaliacaosNrfo->setNotaRetakeFinalOral($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaosNrfo(AlunoAvaliacao $alunoAvaliacaosNrfo): self
    {
        if ($this->alunoAvaliacaosNrfo->contains($alunoAvaliacaosNrfo) === true) {
            $this->alunoAvaliacaosNrfo->removeElement($alunoAvaliacaosNrfo);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaosNrfo->getNotaRetakeFinalOral() === $this) {
                $alunoAvaliacaosNrfo->setNotaRetakeFinalOral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasFoa(): Collection
    {
        return $this->historicoReposicaoAulasFoa;
    }

    public function addHistoricoReposicaoAulasFoa(HistoricoReposicaoAula $historicoReposicaoAulasFoa): self
    {
        if ($this->historicoReposicaoAulasFoa->contains($historicoReposicaoAulasFoa) === false) {
            $this->historicoReposicaoAulasFoa[] = $historicoReposicaoAulasFoa;
            $historicoReposicaoAulasFoa->setFinalOralAnterior($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasFoa(HistoricoReposicaoAula $historicoReposicaoAulasFoa): self
    {
        if ($this->historicoReposicaoAulasFoa->contains($historicoReposicaoAulasFoa) === true) {
            $this->historicoReposicaoAulasFoa->removeElement($historicoReposicaoAulasFoa);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasFoa->getFinalOralAnterior() === $this) {
                $historicoReposicaoAulasFoa->setFinalOralAnterior(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasRmtoa(): Collection
    {
        return $this->historicoReposicaoAulasRmtoa;
    }

    public function addHistoricoReposicaoAulasRmtoa(HistoricoReposicaoAula $historicoReposicaoAulasRmtoa): self
    {
        if ($this->historicoReposicaoAulasRmtoa->contains($historicoReposicaoAulasRmtoa) === false) {
            $this->historicoReposicaoAulasRmtoa[] = $historicoReposicaoAulasRmtoa;
            $historicoReposicaoAulasRmtoa->setRetakeMidTermOralAnterior($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasRmtoa(HistoricoReposicaoAula $historicoReposicaoAulasRmtoa): self
    {
        if ($this->historicoReposicaoAulasRmtoa->contains($historicoReposicaoAulasRmtoa) === true) {
            $this->historicoReposicaoAulasRmtoa->removeElement($historicoReposicaoAulasRmtoa);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasRmtoa->getRetakeMidTermOralAnterior() === $this) {
                $historicoReposicaoAulasRmtoa->setRetakeMidTermOralAnterior(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasRmtoat(): Collection
    {
        return $this->historicoReposicaoAulasRmtoat;
    }

    public function addHistoricoReposicaoAulasRmtoat(HistoricoReposicaoAula $historicoReposicaoAulasRmtoat): self
    {
        if ($this->historicoReposicaoAulasRmtoat->contains($historicoReposicaoAulasRmtoat) === false) {
            $this->historicoReposicaoAulasRmtoat[] = $historicoReposicaoAulasRmtoat;
            $historicoReposicaoAulasRmtoat->setRetakeMidTermOralAtual($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasRmtoat(HistoricoReposicaoAula $historicoReposicaoAulasRmtoat): self
    {
        if ($this->historicoReposicaoAulasRmtoat->contains($historicoReposicaoAulasRmtoat) === true) {
            $this->historicoReposicaoAulasRmtoat->removeElement($historicoReposicaoAulasRmtoat);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasRmtoat->getRetakeMidTermOralAtual() === $this) {
                $historicoReposicaoAulasRmtoat->setRetakeMidTermOralAtual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasRfoa(): Collection
    {
        return $this->historicoReposicaoAulasRfoa;
    }

    public function addHistoricoReposicaoAulasRfoa(HistoricoReposicaoAula $historicoReposicaoAulasRfoa): self
    {
        if ($this->historicoReposicaoAulasRfoa->contains($historicoReposicaoAulasRfoa) === false) {
            $this->historicoReposicaoAulasRfoa[] = $historicoReposicaoAulasRfoa;
            $historicoReposicaoAulasRfoa->setRetakeFinalOralAnterior($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasRfoa(HistoricoReposicaoAula $historicoReposicaoAulasRfoa): self
    {
        if ($this->historicoReposicaoAulasRfoa->contains($historicoReposicaoAulasRfoa) === true) {
            $this->historicoReposicaoAulasRfoa->removeElement($historicoReposicaoAulasRfoa);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasRfoa->getRetakeFinalOralAnterior() === $this) {
                $historicoReposicaoAulasRfoa->setRetakeFinalOralAnterior(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasRfoat(): Collection
    {
        return $this->historicoReposicaoAulasRfoat;
    }

    public function addHistoricoReposicaoAulasRfoat(HistoricoReposicaoAula $historicoReposicaoAulasRfoat): self
    {
        if ($this->historicoReposicaoAulasRfoat->contains($historicoReposicaoAulasRfoat) === false) {
            $this->historicoReposicaoAulasRfoat[] = $historicoReposicaoAulasRfoat;
            $historicoReposicaoAulasRfoat->setRetakeFinalOralAtual($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasRfoat(HistoricoReposicaoAula $historicoReposicaoAulasRfoat): self
    {
        if ($this->historicoReposicaoAulasRfoat->contains($historicoReposicaoAulasRfoat) === true) {
            $this->historicoReposicaoAulasRfoat->removeElement($historicoReposicaoAulasRfoat);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasRfoat->getRetakeFinalOralAtual() === $this) {
                $historicoReposicaoAulasRfoat->setRetakeFinalOralAtual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoReposicaoAula[]
     */
    public function getHistoricoReposicaoAulasFoat(): Collection
    {
        return $this->historicoReposicaoAulasFoat;
    }

    public function addHistoricoReposicaoAulasFoat(HistoricoReposicaoAula $historicoReposicaoAulasFoat): self
    {
        if ($this->historicoReposicaoAulasFoat->contains($historicoReposicaoAulasFoat) === false) {
            $this->historicoReposicaoAulasFoat[] = $historicoReposicaoAulasFoat;
            $historicoReposicaoAulasFoat->setFinalOralAtual($this);
        }

        return $this;
    }

    public function removeHistoricoReposicaoAulasFoat(HistoricoReposicaoAula $historicoReposicaoAulasFoat): self
    {
        if ($this->historicoReposicaoAulasFoat->contains($historicoReposicaoAulasFoat) === true) {
            $this->historicoReposicaoAulasFoat->removeElement($historicoReposicaoAulasFoat);
            // set the owning side to null (unless already changed)
            if ($historicoReposicaoAulasFoat->getFinalOralAtual() === $this) {
                $historicoReposicaoAulasFoat->setFinalOralAtual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceituals(): Collection
    {
        return $this->alunoAvaliacaoConceituals;
    }

    public function addAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === false) {
            $this->alunoAvaliacaoConceituals[] = $alunoAvaliacaoConceitual;
            $alunoAvaliacaoConceitual->setNotaListening1($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === true) {
            $this->alunoAvaliacaoConceituals->removeElement($alunoAvaliacaoConceitual);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitual->getNotaListening1() === $this) {
                $alunoAvaliacaoConceitual->setNotaListening1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceitualsNs(): Collection
    {
        return $this->alunoAvaliacaoConceitualsNs;
    }

    public function addAlunoAvaliacaoConceitualsN(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsN): self
    {
        if ($this->alunoAvaliacaoConceitualsNs->contains($alunoAvaliacaoConceitualsN) === false) {
            $this->alunoAvaliacaoConceitualsNs[] = $alunoAvaliacaoConceitualsN;
            $alunoAvaliacaoConceitualsN->setNotaSpeaking1($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitualsN(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsN): self
    {
        if ($this->alunoAvaliacaoConceitualsNs->contains($alunoAvaliacaoConceitualsN) === true) {
            $this->alunoAvaliacaoConceitualsNs->removeElement($alunoAvaliacaoConceitualsN);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitualsN->getNotaSpeaking1() === $this) {
                $alunoAvaliacaoConceitualsN->setNotaSpeaking1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceitualsNw(): Collection
    {
        return $this->alunoAvaliacaoConceitualsNw;
    }

    public function addAlunoAvaliacaoConceitualsNw(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNw): self
    {
        if ($this->alunoAvaliacaoConceitualsNw->contains($alunoAvaliacaoConceitualsNw) === false) {
            $this->alunoAvaliacaoConceitualsNw[] = $alunoAvaliacaoConceitualsNw;
            $alunoAvaliacaoConceitualsNw->setNotaWriting1($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitualsNw(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNw): self
    {
        if ($this->alunoAvaliacaoConceitualsNw->contains($alunoAvaliacaoConceitualsNw) === true) {
            $this->alunoAvaliacaoConceitualsNw->removeElement($alunoAvaliacaoConceitualsNw);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitualsNw->getNotaWriting1() === $this) {
                $alunoAvaliacaoConceitualsNw->setNotaWriting1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceitualsNl2(): Collection
    {
        return $this->alunoAvaliacaoConceitualsNl2;
    }

    public function addAlunoAvaliacaoConceitualsNl2(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNl2): self
    {
        if ($this->alunoAvaliacaoConceitualsNl2->contains($alunoAvaliacaoConceitualsNl2) === false) {
            $this->alunoAvaliacaoConceitualsNl2[] = $alunoAvaliacaoConceitualsNl2;
            $alunoAvaliacaoConceitualsNl2->setNotaListening2($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitualsNl2(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNl2): self
    {
        if ($this->alunoAvaliacaoConceitualsNl2->contains($alunoAvaliacaoConceitualsNl2) === true) {
            $this->alunoAvaliacaoConceitualsNl2->removeElement($alunoAvaliacaoConceitualsNl2);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitualsNl2->getNotaListening2() === $this) {
                $alunoAvaliacaoConceitualsNl2->setNotaListening2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceitualsNs2(): Collection
    {
        return $this->alunoAvaliacaoConceitualsNs2;
    }

    public function addAlunoAvaliacaoConceitualsNs2(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNs2): self
    {
        if ($this->alunoAvaliacaoConceitualsNs2->contains($alunoAvaliacaoConceitualsNs2) === false) {
            $this->alunoAvaliacaoConceitualsNs2[] = $alunoAvaliacaoConceitualsNs2;
            $alunoAvaliacaoConceitualsNs2->setNotaSpeaking2($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitualsNs2(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNs2): self
    {
        if ($this->alunoAvaliacaoConceitualsNs2->contains($alunoAvaliacaoConceitualsNs2) === true) {
            $this->alunoAvaliacaoConceitualsNs2->removeElement($alunoAvaliacaoConceitualsNs2);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitualsNs2->getNotaSpeaking2() === $this) {
                $alunoAvaliacaoConceitualsNs2->setNotaSpeaking2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacaoConceitual[]
     */
    public function getAlunoAvaliacaoConceitualsNw2(): Collection
    {
        return $this->alunoAvaliacaoConceitualsNw2;
    }

    public function addAlunoAvaliacaoConceitualsNw2(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNw2): self
    {
        if ($this->alunoAvaliacaoConceitualsNw2->contains($alunoAvaliacaoConceitualsNw2) === false) {
            $this->alunoAvaliacaoConceitualsNw2[] = $alunoAvaliacaoConceitualsNw2;
            $alunoAvaliacaoConceitualsNw2->setNotaWriting2($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitualsNw2(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitualsNw2): self
    {
        if ($this->alunoAvaliacaoConceitualsNw2->contains($alunoAvaliacaoConceitualsNw2) === true) {
            $this->alunoAvaliacaoConceitualsNw2->removeElement($alunoAvaliacaoConceitualsNw2);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitualsNw2->getNotaWriting2() === $this) {
                $alunoAvaliacaoConceitualsNw2->setNotaWriting2(null);
            }
        }

        return $this;
    }


}
