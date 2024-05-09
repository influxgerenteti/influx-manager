<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * FollowUp
 *
 * @ORM\Table(name="follow_up",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_ALUNO", columns={"aluno_id"}), @ORM\Index(name="IDX_EMPRESA", columns={"empresa_id"}), @ORM\Index(name="IDX_FUNCIONARIO", columns={"funcionario_id"}), @ORM\Index(name="IDX_ATENDENTE", columns={"atendente_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\FollowUpRepository")
 */
class FollowUp
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
     * @ORM\Column(name="empresa_nome", type="string", length=255, nullable=true)
     */
    private $empresa_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="funcionario_nome", type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="atendente_nome", type="string", length=255, nullable=true)
     */
    private $atendente_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricao", type="string", length=50, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="grau_interesse", type="string", length=50, nullable=true)
     */
    private $grau_interesse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="date", type="string", length=10, nullable=true)
     */
    private $data;

    /**
     * @var string|null
     *
     * @ORM\Column(name="assunto", type="text", nullable=true)
     */
    private $assunto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=2, nullable=true, options={"fixed"=true,"comment"="A - Aberto, RS - Resolvido, RT - Retornado"})
     */
    private $situacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_proximo_contato", type="string", length=10, nullable=true)
     */
    private $data_proximo_contato;

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
     * @var \App\Entity\Importacao\Funcionario
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Funcionario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="atendente_id",                              referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $atendente;

    /**
     * @var \App\Entity\Importacao\Empresa
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empresa_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $empresa;

    /**
     * @var \App\Entity\Importacao\Funcionario
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Funcionario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="funcionario_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $funcionario;

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

    public function getEmpresaNome(): ?string
    {
        return $this->empresa_nome;
    }

    public function setEmpresaNome(?string $empresa_nome): self
    {
        $this->empresa_nome = $empresa_nome;

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

    public function getAtendenteNome(): ?string
    {
        return $this->atendente_nome;
    }

    public function setAtendenteNome(?string $atendente_nome): self
    {
        $this->atendente_nome = $atendente_nome;

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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getGrauInteresse(): ?string
    {
        return $this->grau_interesse;
    }

    public function setGrauInteresse(?string $grau_interesse): self
    {
        $this->grau_interesse = $grau_interesse;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getAssunto()
    {
        return $this->assunto;
    }

    public function setAssunto($assunto): self
    {
        $this->assunto = $assunto;

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

    public function getDataProximoContato(): ?string
    {
        return $this->data_proximo_contato;
    }

    public function setDataProximoContato(?string $data_proximo_contato): self
    {
        $this->data_proximo_contato = $data_proximo_contato;

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

    public function getAtendente(): ?Funcionario
    {
        return $this->atendente;
    }

    public function setAtendente(?Funcionario $atendente): self
    {
        $this->atendente = $atendente;

        return $this;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

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


}
