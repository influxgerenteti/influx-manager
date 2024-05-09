<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\HistoricoSituacaoAlunoRepository")
 * @ORM\Table(name="historico_situacao_aluno")
 */
class HistoricoSituacaoAluno
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="historicoSituacaoAlunos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="historicoSituacaoAlunos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario_alteracao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_alteracao;

    /**
     * @ORM\Column(type="string", length=3, nullable=true, options={"comment"="ATI - Ativo, INA - Inativo, INT - Interessado, TRA - Trancado"})
     */
    private $situacao_anterior;

    /**
     * @ORM\Column(type="string", length=33)
     */
    private $situacao_atual;

    public function __construct()
    {
        $this->data_alteracao = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsuarioAlteracao(): ?Usuario
    {
        return $this->usuario_alteracao;
    }

    public function setUsuarioAlteracao(?Usuario $usuario_alteracao): self
    {
        $this->usuario_alteracao = $usuario_alteracao;

        return $this;
    }

    public function getDataAlteracao(): ?\DateTimeInterface
    {
        return $this->data_alteracao;
    }

    public function setDataAlteracao(\DateTimeInterface $data_alteracao): self
    {
        $this->data_alteracao = $data_alteracao;

        return $this;
    }

    public function getSituacaoAnterior(): ?string
    {
        return $this->situacao_anterior;
    }

    public function setSituacaoAnterior(?string $situacao_anterior): self
    {
        $this->situacao_anterior = $situacao_anterior;

        return $this;
    }

    public function getSituacaoAtual(): ?string
    {
        return $this->situacao_atual;
    }

    public function setSituacaoAtual(string $situacao_atual): self
    {
        $this->situacao_atual = $situacao_atual;

        return $this;
    }


}
