<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunoAvaliacaoRepository")
 * @ORM\Table(name="aluno_avaliacao")
 */
class AlunoAvaliacao
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="alunoAvaliacaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoAvaliacaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro", inversedBy="alunoAvaliacaos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_mid_term_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_mid_term_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_mid_term_composition = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_final_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_final_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_final_composition = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_mid_term_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_mid_term_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_mid_term_composition = null;


    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_final_escrita = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_final_test = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $nota_retake_final_composition = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="aluno_avaliacao")
     */
    private $reposicaoAulas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Turma", inversedBy="alunoAvaliacaos")
     */
    private $turma;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaosNmto")
     */
    private $nota_mid_term_oral;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvalicaosNfo")
     */
    private $nota_final_oral;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaosNrmto")
     */
    private $nota_retake_mid_term_oral;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaosNrfo")
     */
    private $nota_retake_final_oral;

    /**
     * @ORM\Column(type="boolean")
     */
    private $personal = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="alunoAvaliacaos")
     */
    private $contrato;

    public function __construct()
    {
        $this->reposicaoAulas = new ArrayCollection();
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

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

        return $this;
    }

    public function getLivro(): ?Livro
    {
        return $this->livro;
    }

    public function setLivro(?Livro $livro): self
    {
        $this->livro = $livro;

        return $this;
    }

    public function getNotaMidTermEscrita()
    {
        return $this->nota_mid_term_escrita;
    }

    public function setNotaMidTermEscrita($nota_mid_term_escrita): self
    {
        $this->nota_mid_term_escrita = $nota_mid_term_escrita;

        return $this;
    }

    public function getNotaMidTermTest()
    {
        return $this->nota_mid_term_test;
    }

    public function setNotaMidTermTest($nota_mid_term_test): self
    {
        $this->nota_mid_term_test = $nota_mid_term_test;

        return $this;
    }

    public function getNotaMidTermComposition()
    {
        return $this->nota_mid_term_composition;
    }

    public function setNotaMidTermComposition($nota_mid_term_composition): self
    {
        $this->nota_mid_term_composition = $nota_mid_term_composition;

        return $this;
    }

    public function getNotaFinalEscrita()
    {
        return $this->nota_final_escrita;
    }

    public function setNotaFinalEscrita($nota_final_escrita): self
    {
        $this->nota_final_escrita = $nota_final_escrita;

        return $this;
    }

    public function getNotaFinalTest()
    {
        return $this->nota_final_test;
    }

    public function setNotaFinalTest($nota_final_test): self
    {
        $this->nota_final_test = $nota_final_test;

        return $this;
    }

    public function getNotaFinalComposition()
    {
        return $this->nota_final_composition;
    }

    public function setNotaFinalComposition($nota_final_composition): self
    {
        $this->nota_final_composition = $nota_final_composition;

        return $this;
    }

    public function getNotaRetakeMidTermEscrita()
    {
        return $this->nota_retake_mid_term_escrita;
    }

    public function setNotaRetakeMidTermEscrita($nota_retake_mid_term_escrita): self
    {
        $this->nota_retake_mid_term_escrita = $nota_retake_mid_term_escrita;

        return $this;
    }

    public function getNotaRetakeMidTermTest()
    {
        return $this->nota_retake_mid_term_test;
    }

    public function setNotaRetakeMidTermTest($nota_retake_mid_term_test): self
    {
        $this->nota_retake_mid_term_test = $nota_retake_mid_term_test;

        return $this;
    }

    public function getNotaRetakeMidTermComposition()
    {
        return $this->nota_retake_mid_term_composition;
    }

    public function setNotaRetakeMidTermComposition($nota_retake_mid_term_composition): self
    {
        $this->nota_retake_mid_term_composition = $nota_retake_mid_term_composition;

        return $this;
    }

    public function getNotaRetakeFinalEscrita()
    {
        return $this->nota_retake_final_escrita;
    }

    public function setNotaRetakeFinalEscrita($nota_retake_final_escrita): self
    {
        $this->nota_retake_final_escrita = $nota_retake_final_escrita;

        return $this;
    }

    public function getNotaRetakeFinalTest()
    {
        return $this->nota_retake_final_test;
    }

    public function setNotaRetakeFinalTest($nota_retake_final_test): self
    {
        $this->nota_retake_final_test = $nota_retake_final_test;

        return $this;
    }

    public function getNotaRetakeFinalComposition()
    {
        return $this->nota_retake_final_composition;
    }

    public function setNotaRetakeFinalComposition($nota_retake_final_composition): self
    {
        $this->nota_retake_final_composition = $nota_retake_final_composition;

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
            $reposicaoAula->setAlunoAvaliacao($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getAlunoAvaliacao() === $this) {
                $reposicaoAula->setAlunoAvaliacao(null);
            }
        }

        return $this;
    }

    public function getTurma(): ?Turma
    {
        return $this->turma;
    }

    public function setTurma(?Turma $turma): self
    {
        $this->turma = $turma;

        return $this;
    }

    public function getNotaMidTermOral(): ?ConceitoAvaliacao
    {
        return $this->nota_mid_term_oral;
    }

    public function setNotaMidTermOral(?ConceitoAvaliacao $nota_mid_term_oral): self
    {
        $this->nota_mid_term_oral = $nota_mid_term_oral;

        return $this;
    }

    public function getNotaFinalOral(): ?ConceitoAvaliacao
    {
        return $this->nota_final_oral;
    }

    public function setNotaFinalOral(?ConceitoAvaliacao $nota_final_oral): self
    {
        $this->nota_final_oral = $nota_final_oral;

        return $this;
    }

    public function getNotaRetakeMidTermOral(): ?ConceitoAvaliacao
    {
        return $this->nota_retake_mid_term_oral;
    }

    public function setNotaRetakeMidTermOral(?ConceitoAvaliacao $nota_retake_mid_term_oral): self
    {
        $this->nota_retake_mid_term_oral = $nota_retake_mid_term_oral;

        return $this;
    }

    public function getNotaRetakeFinalOral(): ?ConceitoAvaliacao
    {
        return $this->nota_retake_final_oral;
    }

    public function setNotaRetakeFinalOral(?ConceitoAvaliacao $nota_retake_final_oral): self
    {
        $this->nota_retake_final_oral = $nota_retake_final_oral;

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

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

        return $this;
    }


}
