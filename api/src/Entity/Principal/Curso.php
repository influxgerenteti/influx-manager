<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CursoRepository")
 */
class Curso
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Idioma")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idioma;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $sigla;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ModalidadeTurma")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modalidade_turma;

    /**
     *
     * @ORM\Column(type="boolean",options={"default":"1"})
     */
    private $intensidade_regular = true;

    /**
     *
     * @ORM\Column(type="boolean",options={"default":"0"})
     */
    private $intensidade_semi_intensivo = false;

    /**
     *
     * @ORM\Column(type="boolean",options={"default":"0"})
     */
    private $intensidade_intensivo = false;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Livro", mappedBy="curso")
     */
    private $livros;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="curso")
     */
    private $contratosCursos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Turma", mappedBy="curso")
     */
    private $turmas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Interessado", mappedBy="curso")
     */
    private $interessados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="curso")
     */
    private $followupComercials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoDiario", mappedBy="curso")
     */
    private $alunoDiarios;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idade_minima;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idade_maxima;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item", inversedBy="cursos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $servico;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_course_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_course_reference_code;

    public function __construct()
    {
        $this->livros          = new ArrayCollection();
        $this->contratosCursos = new ArrayCollection();
        $this->turmas          = new ArrayCollection();
        $this->interessados    = new ArrayCollection();
        $this->followupComercials = new ArrayCollection();
        $this->alunoDiarios       = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdioma(): ?Idioma
    {
        return $this->idioma;
    }

    public function setIdioma(?Idioma $idioma): self
    {
        $this->idioma = $idioma;

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

    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    public function setSigla(string $sigla): self
    {
        $this->sigla = $sigla;

        return $this;
    }

    public function getModalidadeTurma() : ? ModalidadeTurma
    {
        return $this->modalidade_turma;
    }

    public function setModalidadeTurma(? ModalidadeTurma $modalidade_turma) : self
    {
        $this->modalidade_turma = $modalidade_turma;

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

    public function getIntensidadeRegular(): ?bool
    {
        return $this->intensidade_regular;
    }

    public function setIntensidadeRegular(bool $intensidade_regular): self
    {
        $this->intensidade_regular = $intensidade_regular;

        return $this;
    }

    public function getIntensidadeSemiIntensivo(): ?bool
    {
        return $this->intensidade_semi_intensivo;
    }

    public function setIntensidadeSemiIntensivo(bool $intensidade_semi_intensivo): self
    {
        $this->intensidade_semi_intensivo = $intensidade_semi_intensivo;

        return $this;
    }

    public function getIntensidadeIntensivo(): ?bool
    {
        return $this->intensidade_intensivo;
    }

    public function setIntensidadeIntensivo(bool $intensidade_intensivo): self
    {
        $this->intensidade_intensivo = $intensidade_intensivo;

        return $this;
    }

    /**
     * @return Collection|Livro[]
     */
    public function getLivros(): Collection
    {
        return $this->livros;
    }

    public function addLivro(Livro $livro): self
    {
        if ($this->livros->contains($livro) === false) {
            $this->livros[] = $livro;
            $livro->addCurso($this);
        }

        return $this;
    }

    public function removeLivro(Livro $livro): self
    {
        if ($this->livros->contains($livro) === true) {
            $this->livros->removeElement($livro);
            $livro->removeCurso($this);
        }

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratosCursos(): Collection
    {
        return $this->contratosCursos;
    }

    public function addContratosCurso(Contrato $contratosCurso): self
    {
        if ($this->contratosCursos->contains($contratosCurso) === false) {
            $this->contratosCursos[] = $contratosCurso;
            $contratosCurso->setCurso($this);
        }

        return $this;
    }

    public function removeContratosCurso(Contrato $contratosCurso): self
    {
        if ($this->contratosCursos->contains($contratosCurso) === true) {
            $this->contratosCursos->removeElement($contratosCurso);
            // set the owning side to null (unless already changed)
            if ($contratosCurso->getCurso() === $this) {
                $contratosCurso->setCurso(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Turma[]
     */
    public function getTurmas(): Collection
    {
        return $this->turmas;
    }

    public function addTurma(Turma $turma): self
    {
        if ($this->turmas->contains($turma) === false) {
            $this->turmas[] = $turma;
            $turma->setCurso($this);
        }

        return $this;
    }

    public function removeTurma(Turma $turma): self
    {
        if ($this->turmas->contains($turma) === true) {
            $this->turmas->removeElement($turma);
            // set the owning side to null (unless already changed)
            if ($turma->getCurso() === $this) {
                $turma->setCurso(null);
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
            $interessado->setCurso($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === true) {
            $this->interessados->removeElement($interessado);
            // set the owning side to null (unless already changed)
            if ($interessado->getCurso() === $this) {
                $interessado->setCurso(null);
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
            $followupComercial->setCursoPretendido($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getCursoPretendido() === $this) {
                $followupComercial->setCursoPretendido(null);
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
            $alunoDiario->setCurso($this);
        }

        return $this;
    }

    public function removeAlunoDiario(AlunoDiario $alunoDiario): self
    {
        if ($this->alunoDiarios->contains($alunoDiario) === true) {
            $this->alunoDiarios->removeElement($alunoDiario);
            // set the owning side to null (unless already changed)
            if ($alunoDiario->getCurso() === $this) {
                $alunoDiario->setCurso(null);
            }
        }

        return $this;
    }

    public function getIdadeMinima(): ?int
    {
        return $this->idade_minima;
    }

    public function setIdadeMinima(?int $idade_minima): self
    {
        $this->idade_minima = $idade_minima;

        return $this;
    }

    public function getIdadeMaxima(): ?int
    {
        return $this->idade_maxima;
    }

    public function setIdadeMaxima(?int $idade_maxima): self
    {
        $this->idade_maxima = $idade_maxima;

        return $this;
    }

    public function getServico(): ?Item
    {
        return $this->servico;
    }

    public function setServico(?Item $servico): self
    {
        $this->servico = $servico;

        return $this;
    }



    /**
     * Get the value of portal_course_id
     */
    public function getPortal_course_id()
    {
        return $this->portal_course_id;
    }

    /**
     * Set the value of portal_course_id
     *
     * @return self
     */
    public function setPortal_course_id($portal_course_id)
    {
        $this->portal_course_id = $portal_course_id;

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


}
