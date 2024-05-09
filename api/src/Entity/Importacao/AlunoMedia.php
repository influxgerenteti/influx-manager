<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlunoMedia
 *
 * @ORM\Table(name="aluno_media",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_ALUNO", columns={"aluno_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\AlunoMediaRepository")
 */
class AlunoMedia
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
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="A - Aprovado, R - Reprovado"})
     */
    private $situacao;

    /**
     * @var \App\Entity\Importacao\Aluno
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Aluno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aluno_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
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

    public function getAlunoNome(): ?string
    {
        return $this->aluno_nome;
    }

    public function setAlunoNome(?string $aluno_nome): self
    {
        $this->aluno_nome = $aluno_nome;

        return $this;
    }

    public function getNota(): ?string
    {
        return $this->nota;
    }

    public function setNota(?string $nota): self
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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(?string $situacao): self
    {
        $this->situacao = $situacao;

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
