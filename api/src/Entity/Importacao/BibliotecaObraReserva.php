<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliotecaObraReserva
 *
 * @ORM\Table(name="biblioteca_obra_reserva",                                               indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_ALUNO", columns={"aluno_id"}), @ORM\Index(name="IDX_FUNCIONARIO", columns={"funcionario_id"}), @ORM\Index(name="IDX_BIBLIOTECA_OBRA", columns={"biblioteca_obra_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\BibliotecaObraReservaRepository")
 */
class BibliotecaObraReserva
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
     * @ORM\Column(name="funcionario_nome", type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="biblioteca_obra_nome", type="string", length=255, nullable=true)
     */
    private $biblioteca_obra_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero_exemplares", type="string", length=20, nullable=true)
     */
    private $numero_exemplares;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome", type="string", length=100, nullable=true)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_reserva", type="string", length=30, nullable=true)
     */
    private $data_reserva;

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
     * @var \App\Entity\Importacao\BibliotecaObra
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\BibliotecaObra")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="biblioteca_obra_id",                           referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $biblioteca_obra;

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

    public function getFuncionarioNome(): ?string
    {
        return $this->funcionario_nome;
    }

    public function setFuncionarioNome(?string $funcionario_nome): self
    {
        $this->funcionario_nome = $funcionario_nome;

        return $this;
    }

    public function getBibliotecaObraNome(): ?string
    {
        return $this->biblioteca_obra_nome;
    }

    public function setBibliotecaObraNome(?string $biblioteca_obra_nome): self
    {
        $this->biblioteca_obra_nome = $biblioteca_obra_nome;

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

    public function getNumeroExemplares(): ?string
    {
        return $this->numero_exemplares;
    }

    public function setNumeroExemplares(?string $numero_exemplares): self
    {
        $this->numero_exemplares = $numero_exemplares;

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

    public function getDataReserva(): ?string
    {
        return $this->data_reserva;
    }

    public function setDataReserva(?string $data_reserva): self
    {
        $this->data_reserva = $data_reserva;

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

    public function getBibliotecaObra(): ?BibliotecaObra
    {
        return $this->biblioteca_obra;
    }

    public function setBibliotecaObra(?BibliotecaObra $biblioteca_obra): self
    {
        $this->biblioteca_obra = $biblioteca_obra;

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
