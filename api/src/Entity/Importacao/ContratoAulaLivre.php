<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\ContratoAulaLivreRepository")
 * @ORM\Table(name="contrato_aula_livre")
 */
class ContratoAulaLivre
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Contrato", inversedBy="contratoAulaLivres")
     * @ORM\JoinColumn(name="contrato_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Funcionario", inversedBy="contratoAulaLivres")
     * @ORM\JoinColumn(name="funcionario_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Aluno", inversedBy="contratoAulaLivres")
     * @ORM\JoinColumn(name="aluno_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $aluno;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero_contrato;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aluno_nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usuario;

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

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

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

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

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

    public function getAlunoNome(): ?string
    {
        return $this->aluno_nome;
    }

    public function setAlunoNome(?string $aluno_nome): self
    {
        $this->aluno_nome = $aluno_nome;

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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

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

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }


}
