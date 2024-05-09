<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * AulaLivre
 *
 * @ORM\Table(name="aula_livre",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_ALUNO", columns={"aluno_id"}), @ORM\Index(name="IDX_ESTAGIO", columns={"estagio_id"}), @ORM\Index(name="IDX_FUNCIONARIO", columns={"funcionario_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\AulaLivreRepository")
 */
class AulaLivre
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
     * @ORM\Column(name="descricao", type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="string", length=30, nullable=true)
     */
    private $data;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horario_inicial", type="string", length=30, nullable=true)
     */
    private $horario_inicial;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horario_final", type="string", length=30, nullable=true)
     */
    private $horario_final;

    /**
     * @var string|null
     *
     * @ORM\Column(name="licao", type="string", length=100, nullable=true)
     */
    private $licao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="C - Cancelada, F - Falta, N - Não Realizada, P - Presença"})
     */
    private $situacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pago", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $pago;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tipo_aula", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="A - Aula Livre, R - Reposição"})
     */
    private $tipo_aula;

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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

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

    public function getHorarioInicial(): ?string
    {
        return $this->horario_inicial;
    }

    public function setHorarioInicial(?string $horario_inicial): self
    {
        $this->horario_inicial = $horario_inicial;

        return $this;
    }

    public function getHorarioFinal(): ?string
    {
        return $this->horario_final;
    }

    public function setHorarioFinal(?string $horario_final): self
    {
        $this->horario_final = $horario_final;

        return $this;
    }

    public function getLicao(): ?string
    {
        return $this->licao;
    }

    public function setLicao(?string $licao): self
    {
        $this->licao = $licao;

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

    public function getPago(): ?string
    {
        return $this->pago;
    }

    public function setPago(?string $pago): self
    {
        $this->pago = $pago;

        return $this;
    }

    public function getTipoAula(): ?string
    {
        return $this->tipo_aula;
    }

    public function setTipoAula(?string $tipo_aula): self
    {
        $this->tipo_aula = $tipo_aula;

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
