<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\LivroRepository")
 */
class Livro
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanejamentoLicao")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planejamento_licao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro")
     */
    private $proximo_livro;

    /**
     *
     * @ORM\Column(type="string", length=1, options={"comment":"A - ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Curso", inversedBy="livros")
     */
    private $curso;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="livro")
     */
    private $contratos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="livro")
     */
    private $alunoDiarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacao", mappedBy="livro")
     */
    private $alunoAvaliacaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAvaliacaoConceitual", mappedBy="livro")
     */
    private $alunoAvaliacaoConceituals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="livro")
     */
    private $reposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\InteressadoAtividadeExtra", mappedBy="livro")
     */
    private $interessadoAtividadeExtras;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_level_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_course_reference_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_level_reference_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hybrid_id;

    public function __construct()
    {
        $this->curso           = new ArrayCollection();
        $this->contratos       = new ArrayCollection();
        $this->alunoDiarios    = new ArrayCollection();
        $this->alunoAvaliacaos = new ArrayCollection();
        $this->alunoAvaliacaoConceituals = new ArrayCollection();
        $this->reposicaoAulas            = new ArrayCollection();
        $this->interessadoAtividadeExtras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

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

    public function getProximoLivro(): ?self
    {
        return $this->proximo_livro;
    }

    public function setProximoLivro(?self $proximo_livro): self
    {
        $this->proximo_livro = $proximo_livro;

        return $this;
    }

    public function getSituacao()
    {
        return $this->situacao;
    }

    public function setSituacao($situacao): self
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
     * @return Collection|Curso[]
     */
    public function getCurso(): Collection
    {
        return $this->curso;
    }

    public function addCurso(Curso $curso): self
    {
        if ($this->curso->contains($curso) === false) {
            $this->curso[] = $curso;
        }

        return $this;
    }

    public function removeCurso(Curso $curso): self
    {
        if ($this->curso->contains($curso) === true) {
            $this->curso->removeElement($curso);
        }

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
            $contrato->setLivro($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getLivro() === $this) {
                $contrato->setLivro(null);
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
            $alunoDiario->setLivro($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getLivro() === $this) {
                $alunoDiario->setLivro(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AlunoAvaliacao[]
     */
    public function getAlunoAvaliacaos(): Collection
    {
        return $this->alunoAvaliacaos;
    }

    public function addAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === false) {
            $this->alunoAvaliacaos[] = $alunoAvaliacao;
            $alunoAvaliacao->setLivro($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacao(AlunoAvaliacao $alunoAvaliacao): self
    {
        if ($this->alunoAvaliacaos->contains($alunoAvaliacao) === true) {
            $this->alunoAvaliacaos->removeElement($alunoAvaliacao);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacao->getLivro() === $this) {
                $alunoAvaliacao->setLivro(null);
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
            $alunoAvaliacaoConceitual->setLivro($this);
        }

        return $this;
    }

    public function removeAlunoAvaliacaoConceitual(AlunoAvaliacaoConceitual $alunoAvaliacaoConceitual): self
    {
        if ($this->alunoAvaliacaoConceituals->contains($alunoAvaliacaoConceitual) === true) {
            $this->alunoAvaliacaoConceituals->removeElement($alunoAvaliacaoConceitual);
            // set the owning side to null (unless already changed)
            if ($alunoAvaliacaoConceitual->getLivro() === $this) {
                $alunoAvaliacaoConceitual->setLivro(null);
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
            $reposicaoAula->setLivro($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getLivro() === $this) {
                $reposicaoAula->setLivro(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InteressadoAtividadeExtra[]
     */
    public function getInteressadoAtividadeExtras(): Collection
    {
        return $this->interessadoAtividadeExtras;
    }

    public function addInteressadoAtividadeExtra(InteressadoAtividadeExtra $interessadoAtividadeExtra): self
    {
        if ($this->interessadoAtividadeExtras->contains($interessadoAtividadeExtra) === false) {
            $this->interessadoAtividadeExtras[] = $interessadoAtividadeExtra;
            $interessadoAtividadeExtra->setLivro($this);
        }

        return $this;
    }

    public function removeInteressadoAtividadeExtra(InteressadoAtividadeExtra $interessadoAtividadeExtra): self
    {
        if ($this->interessadoAtividadeExtras->contains($interessadoAtividadeExtra) === true) {
            $this->interessadoAtividadeExtras->removeElement($interessadoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($interessadoAtividadeExtra->getLivro() === $this) {
                $interessadoAtividadeExtra->setLivro(null);
            }
        }

        return $this;
    }



    /**
     * Get the value of portal_level_id
     */
    public function getPortal_level_id()
    {
        return $this->portal_level_id;
    }

    /**
     * Set the value of portal_level_id
     *
     * @return self
     */
    public function setPortal_level_id($portal_level_id)
    {
        $this->portal_level_id = $portal_level_id;

        return $this;
    }

    /**
     * Get the value of portal_course_reference_code
     */
    public function getPortal_course_reference_code()
    {
        return $this->portal_course_reference_code;
    }

    /**
     * Set the value of portal_course_reference_code
     *
     * @return self
     */
    public function setPortal_course_reference_code($portal_course_reference_code)
    {
        $this->portal_course_reference_code = $portal_course_reference_code;

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



    /**
     * Get the value of portal_level_reference_code
     */
    public function getPortal_level_reference_code()
    {
        return $this->portal_level_reference_code;
    }

    /**
     * Set the value of portal_level_reference_code
     *
     * @return self
     */
    public function setPortal_level_reference_code($portal_level_reference_code)
    {
        $this->portal_level_reference_code = $portal_level_reference_code;

        return $this;
    }


}
