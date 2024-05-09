<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlunoNota
 *
 * @ORM\Table(name="aluno_nota",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_ALUNO", columns={"aluno_id"}), @ORM\Index(name="IDX_TURMA", columns={"turma_id"}), @ORM\Index(name="IDX_ESTAGIO", columns={"estagio_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\AlunoNotaRepository")
 */
class AlunoNota
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="franqueada_id", type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="aluno_nome", type="string", length=255, nullable=true)
     */
    private $aluno_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="turma_nome", type="string", length=255, nullable=true)
     */
    private $turma_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estagio_nome", type="string", length=255, nullable=true)
     */
    private $estagio_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome_prova", type="string", length=100, nullable=true)
     */
    private $nome_prova;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome_avaliacao", type="string", length=100, nullable=true)
     */
    private $nome_avaliacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nota", type="string", length=50, nullable=true)
     */
    private $nota;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conceito", type="string", length=4, nullable=true)
     */
    private $conceito;

    /**
     * @var \App\Entity\Importacao\Aluno
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Aluno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aluno_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $aluno;

    /**
     * @var \App\Entity\Importacao\Estagio
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Estagio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estagio_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $estagio;

    /**
     * @var \App\Entity\Importacao\Turma
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Turma")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="turma_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $turma;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(int $franqueada_id): self
    {
        $this->franqueada_id = $franqueada_id;

        return $this;
    }

    public function getAlunoNome(): ?string
    {
        return $this->aluno_nome;
    }

    public function setAlunoNome(?string $aluno_nome): self
    {
        $this->aluno_nome = $aluno_nome;

        return $this;
    }

    public function getTurmaNome(): ?string
    {
        return $this->turma_nome;
    }

    public function setTurmaNome(?string $turma_nome): self
    {
        $this->turma_nome = $turma_nome;

        return $this;
    }

    public function getEstagioNome(): ?string
    {
        return $this->estagio_nome;
    }

    public function setEstagioNome(?string $estagio_nome): self
    {
        $this->estagio_nome = $estagio_nome;

        return $this;
    }

    public function getNomeProva(): ?string
    {
        return $this->nome_prova;
    }

    public function setNomeProva(?string $nome_prova): self
    {
        $this->nome_prova = $nome_prova;

        return $this;
    }

    public function getNomeAvaliacao(): ?string
    {
        return $this->nome_avaliacao;
    }

    public function setNomeAvaliacao(?string $nome_avaliacao): self
    {
        $this->nome_avaliacao = $nome_avaliacao;

        return $this;
    }

    public function getNota()
    {
        return $this->nota;
    }

    public function setNota($nota): self
    {
        $this->nota = $nota;

        return $this;
    }

    public function getConceito(): ?string
    {
        return $this->conceito;
    }

    public function setConceito(?string $conceito): self
    {
        $this->conceito = $conceito;

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

    public function getEstagio(): ?Estagio
    {
        return $this->estagio;
    }

    public function setEstagio(?Estagio $estagio): self
    {
        $this->estagio = $estagio;

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


}
