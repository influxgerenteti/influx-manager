<?php

namespace App\Entity\Importacao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\ContratoRepository")
 */
class Contrato
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
    private $franqueada_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero_contrato;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"M - Matrícula, R - Rematrícula, T - Transf. Unidade (Entrada)"})
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Aluno", inversedBy="contratos")
     * @ORM\JoinColumn(name="aluno_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $aluno;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aluno_nome;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Responsavel", inversedBy="contratos")
     * @ORM\JoinColumn(name="responsavel_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $responsavel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsavel_nome;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Funcionario", inversedBy="contratos")
     * @ORM\JoinColumn(name="funcionario_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $funcionario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Turma", inversedBy="contratos")
     * @ORM\JoinColumn(name="turma_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $turma;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $turma_nome;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Curso", inversedBy="contratos")
     * @ORM\JoinColumn(name="curso_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $curso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $curso_nome;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Estagio", inversedBy="contratos")
     * @ORM\JoinColumn(name="estagio_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $estagio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estagio_nome;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_inicio;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_termino;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_contrato;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_matricula;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"C - Cancelado, E - Encerrado, R - Rescindido, V - Vigente, S - Sem Status"})
     */
    private $situacao;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $percentual_empresa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContratoAulaLivre", mappedBy="contrato")
     */
    private $contratoAulaLivres;

    public function __construct()
    {
        $this->contratoAulaLivres = new ArrayCollection();
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

    public function getNumeroContrato(): ?string
    {
        return $this->numero_contrato;
    }

    public function setNumeroContrato(?string $numero_contrato): self
    {
        $this->numero_contrato = $numero_contrato;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

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

    public function getAlunoNome(): ?string
    {
        return $this->aluno_nome;
    }

    public function setAlunoNome(?string $aluno_nome): self
    {
        $this->aluno_nome = $aluno_nome;

        return $this;
    }

    public function getResponsavel(): ?Responsavel
    {
        return $this->responsavel;
    }

    public function setResponsavel(?Responsavel $responsavel): self
    {
        $this->responsavel = $responsavel;

        return $this;
    }

    public function getResponsavelNome(): ?string
    {
        return $this->responsavel_nome;
    }

    public function setResponsavelNome(?string $responsavel_nome): self
    {
        $this->responsavel_nome = $responsavel_nome;

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

    public function getFuncionarioNome(): ?string
    {
        return $this->funcionario_nome;
    }

    public function setFuncionarioNome(?string $funcionario_nome): self
    {
        $this->funcionario_nome = $funcionario_nome;

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

    public function getTurmaNome(): ?string
    {
        return $this->turma_nome;
    }

    public function setTurmaNome(?string $turma_nome): self
    {
        $this->turma_nome = $turma_nome;

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

    public function getCursoNome(): ?string
    {
        return $this->curso_nome;
    }

    public function setCursoNome(?string $curso_nome): self
    {
        $this->curso_nome = $curso_nome;

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

    public function getEstagioNome(): ?string
    {
        return $this->estagio_nome;
    }

    public function setEstagioNome(?string $estagio_nome): self
    {
        $this->estagio_nome = $estagio_nome;

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

    public function getDataContrato(): ?string
    {
        return $this->data_contrato;
    }

    public function setDataContrato(?string $data_contrato): self
    {
        $this->data_contrato = $data_contrato;

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

    public function setSituacao(?string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getPercentualEmpresa()
    {
        return $this->percentual_empresa;
    }

    public function setPercentualEmpresa($percentual_empresa): self
    {
        $this->percentual_empresa = $percentual_empresa;

        return $this;
    }

    /**
     * @return Collection|ContratoAulaLivre[]
     */
    public function getContratoAulaLivres(): Collection
    {
        return $this->contratoAulaLivres;
    }

    public function addContratoAulaLivre(ContratoAulaLivre $contratoAulaLivres): self
    {
        if ($this->contratoAulaLivres->contains($contratoAulaLivres) === false) {
            $this->contratoAulaLivres[] = $$contratoAulaLivres;
            $contratoAulaLivres->setContrato($this);
        }

        return $this;
    }

    public function removeContratoAulaLivre(ContratoAulaLivre $contratoAulaLivres): self
    {
        if ($this->contratoAulaLivres->contains($contratoAulaLivres) === true) {
            $this->contratoAulaLivres->removeElement($contratoAulaLivres);
            // set the owning side to null (unless already changed)
            if ($contratoAulaLivres->getContrato() === $this) {
                $contratoAulaLivres->setContrato(null);
            }
        }

        return $this;
    }


}
