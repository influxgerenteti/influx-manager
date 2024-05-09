<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlunoTurma
 *
 * @ORM\Table(name="aluno_turma",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_TURMA", columns={"turma_id"}), @ORM\Index(name="IDX_ALUNO", columns={"aluno_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\AlunoTurmaRepository")
 */
class AlunoTurma
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
     * @ORM\Column(name="codigo_aluno", type="string", length=20, nullable=true)
     */
    private $codigo_aluno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="turma_nome", type="string", length=255, nullable=true)
     */
    private $turma_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo_turma", type="string", length=20, nullable=true)
     */
    private $codigo_turma;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_matricula", type="string", length=10, nullable=true)
     */
    private $data_matricula;

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

    public function getCodigoAluno(): ?string
    {
        return $this->codigo_aluno;
    }

    public function setCodigoAluno(?string $codigo_aluno): self
    {
        $this->codigo_aluno = $codigo_aluno;

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

    public function getCodigoTurma(): ?string
    {
        return $this->codigo_turma;
    }

    public function setCodigoTurma(?string $codigo_turma): self
    {
        $this->codigo_turma = $codigo_turma;

        return $this;
    }

    public function getDataMatricula(): ?string
    {
        return $this->data_matricula;
    }

    public function setDataMatricula(?string $data_matricula): self
    {
        $this->data_matricula = $data_matricula;

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
