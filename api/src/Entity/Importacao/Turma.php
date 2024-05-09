<?php

namespace App\Entity\Importacao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Turma
 *
 * @ORM\Table(name="turma",                                                 indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_SALA", columns={"sala_id"}), @ORM\Index(name="IDX_CURSO", columns={"curso_id"}), @ORM\Index(name="IDX_ESTAGIO", columns={"estagio_id"}), @ORM\Index(name="IDX_FUNCIONARIO", columns={"funcionario_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\TurmaRepository")
 */
class Turma
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
     * @ORM\Column(name="sala_nome", type="string", length=255, nullable=true)
     */
    private $sala_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="curso_nome", type="string", length=255, nullable=true)
     */
    private $curso_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estagio_nome", type="string", length=255, nullable=true)
     */
    private $estagio_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="funcionario_nome", type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_inicio", type="string", length=10, nullable=true)
     */
    private $data_inicio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_termino", type="string", length=10, nullable=true)
     */
    private $data_termino;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horario", type="string", length=255, nullable=true)
     */
    private $horario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricao", type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero_alunos", type="string", length=10, nullable=true)
     */
    private $numero_alunos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="duracao_aula", type="string", length=10, nullable=true)
     */
    private $duracao_aula;

    /**
     * @var string|null
     *
     * @ORM\Column(name="local_aula", type="string", length=255, nullable=true)
     */
    private $local_aula;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ultima_ligacao", type="string", length=255, nullable=true)
     */
    private $ultima_ligacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valor_turma", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valor_turma;

    /**
     * @var \App\Entity\Importacao\Curso
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Curso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="curso_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $curso;

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
     * @var \App\Entity\Importacao\Funcionario
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Funcionario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="funcionario_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $funcionario;

    /**
     * @var \App\Entity\Importacao\Sala
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Sala")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sala_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $sala;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Contrato", mappedBy="turma")
     */
    private $contratos;

    public function __construct()
    {
        $this->contratos = new ArrayCollection();
    }

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

    public function getSalaNome(): ?string
    {
        return $this->sala_nome;
    }

    public function setSalaNome(?string $sala_nome): self
    {
        $this->sala_nome = $sala_nome;

        return $this;
    }

    public function getCursoNome(): ?string
    {
        return $this->curso_nome;
    }

    public function setCursoNome(?string $curso_nome): self
    {
        $this->curso_nome = $curso_nome;

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

    public function getFuncionarioNome(): ?string
    {
        return $this->funcionario_nome;
    }

    public function setFuncionarioNome(?string $funcionario_nome): self
    {
        $this->funcionario_nome = $funcionario_nome;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDataInicio(): ?string
    {
        return $this->data_inicio;
    }

    public function setDataInicio(?string $data_inicio): self
    {
        $this->data_inicio = $data_inicio;

        return $this;
    }

    public function getDataTermino(): ?string
    {
        return $this->data_termino;
    }

    public function setDataTermino(?string $data_termino): self
    {
        $this->data_termino = $data_termino;

        return $this;
    }

    public function getHorario(): ?string
    {
        return $this->horario;
    }

    public function setHorario(?string $horario): self
    {
        $this->horario = $horario;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getNumeroAlunos(): ?string
    {
        return $this->numero_alunos;
    }

    public function setNumeroAlunos(?string $numero_alunos): self
    {
        $this->numero_alunos = $numero_alunos;

        return $this;
    }

    public function getDuracaoAula(): ?string
    {
        return $this->duracao_aula;
    }

    public function setDuracaoAula(?string $duracao_aula): self
    {
        $this->duracao_aula = $duracao_aula;

        return $this;
    }

    public function getLocalAula(): ?string
    {
        return $this->local_aula;
    }

    public function setLocalAula(?string $local_aula): self
    {
        $this->local_aula = $local_aula;

        return $this;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getUltimaLigacao(): ?string
    {
        return $this->ultima_ligacao;
    }

    public function setUltimaLigacao(?string $ultima_ligacao): self
    {
        $this->ultima_ligacao = $ultima_ligacao;

        return $this;
    }

    public function getValorTurma()
    {
        return $this->valor_turma;
    }

    public function setValorTurma($valor_turma): self
    {
        $this->valor_turma = $valor_turma;

        return $this;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

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

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

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
            $contrato->setTurma($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getTurma() === $this) {
                $contrato->setTurma(null);
            }
        }

        return $this;
    }


}
