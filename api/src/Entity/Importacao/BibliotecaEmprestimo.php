<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliotecaEmprestimo
 *
 * @ORM\Table(name="biblioteca_emprestimo",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\BibliotecaEmprestimoRepository")
 */
class BibliotecaEmprestimo
{
    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aluno_nome;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numero_exemplares;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $data_emprestimo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $data_prevista;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $data_devolucao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $renovacao;

    /**
     *
     * @var \App\Entity\Importacao\Aluno
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Aluno")
     * @ORM\JoinColumn(nullable=true,                              onDelete="SET NULL")
     */
    private $aluno;

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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

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

    public function getNumeroExemplares(): ?string
    {
        return $this->numero_exemplares;
    }

    public function setNumeroExemplares(?string $numero_exemplares): self
    {
        $this->numero_exemplares = $numero_exemplares;

        return $this;
    }

    public function getDataEmprestimo(): ?string
    {
        return $this->data_emprestimo;
    }

    public function setDataEmprestimo(?string $data_emprestimo): self
    {
        $this->data_emprestimo = $data_emprestimo;

        return $this;
    }

    public function getDataPrevista(): ?string
    {
        return $this->data_prevista;
    }

    public function setDataPrevista(?string $data_prevista): self
    {
        $this->data_prevista = $data_prevista;

        return $this;
    }

    public function getDataDevolucao(): ?string
    {
        return $this->data_devolucao;
    }

    public function setDataDevolucao(?string $data_devolucao): self
    {
        $this->data_devolucao = $data_devolucao;

        return $this;
    }

    public function getRenovacao(): ?string
    {
        return $this->renovacao;
    }

    public function setRenovacao(?string $renovacao): self
    {
        $this->renovacao = $renovacao;

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


}
