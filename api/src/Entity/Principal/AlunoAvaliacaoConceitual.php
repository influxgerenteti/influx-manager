<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunoAvaliacaoConceitualRepository")
 * @ORM\Table(name="aluno_avaliacao_conceitual")
 */
class AlunoAvaliacaoConceitual
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="alunoAvaliacaoConceituals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoAvaliacaoConceituals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro", inversedBy="alunoAvaliacaoConceituals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Turma", inversedBy="alunoAvaliacaoConceituals")
     */
    private $turma;

    /**
     * @ORM\Column(type="boolean")
     */
    private $personal = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="alunoAvaliacaoConceituals")
     */
    private $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaoConceituals")
     */
    private $nota_listening_1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaoConceitualsNs")
     */
    private $nota_speaking_1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaoConceitualsNw")
     */
    private $nota_writing_1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaoConceitualsNl2")
     */
    private $nota_listening_2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaoConceitualsNs2")
     */
    private $nota_speaking_2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="alunoAvaliacaoConceitualsNw2")
     */
    private $nota_writing_2;

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

    public function getTurma(): ?Turma
    {
        return $this->turma;
    }

    public function setTurma(?Turma $turma): self
    {
        $this->turma = $turma;

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

    public function getNotaListening1(): ?ConceitoAvaliacao
    {
        return $this->nota_listening_1;
    }

    public function setNotaListening1(?ConceitoAvaliacao $nota_listening_1): self
    {
        $this->nota_listening_1 = $nota_listening_1;

        return $this;
    }

    public function getNotaSpeaking1(): ?ConceitoAvaliacao
    {
        return $this->nota_speaking_1;
    }

    public function setNotaSpeaking1(?ConceitoAvaliacao $nota_speaking_1): self
    {
        $this->nota_speaking_1 = $nota_speaking_1;

        return $this;
    }

    public function getNotaWriting1(): ?ConceitoAvaliacao
    {
        return $this->nota_writing_1;
    }

    public function setNotaWriting1(?ConceitoAvaliacao $nota_writing_1): self
    {
        $this->nota_writing_1 = $nota_writing_1;

        return $this;
    }

    public function getNotaListening2(): ?ConceitoAvaliacao
    {
        return $this->nota_listening_2;
    }

    public function setNotaListening2(?ConceitoAvaliacao $nota_listening_2): self
    {
        $this->nota_listening_2 = $nota_listening_2;

        return $this;
    }

    public function getNotaSpeaking2(): ?ConceitoAvaliacao
    {
        return $this->nota_speaking_2;
    }

    public function setNotaSpeaking2(?ConceitoAvaliacao $nota_speaking_2): self
    {
        $this->nota_speaking_2 = $nota_speaking_2;

        return $this;
    }

    public function getNotaWriting2(): ?ConceitoAvaliacao
    {
        return $this->nota_writing_2;
    }

    public function setNotaWriting2(?ConceitoAvaliacao $nota_writing_2): self
    {
        $this->nota_writing_2 = $nota_writing_2;

        return $this;
    }


}
