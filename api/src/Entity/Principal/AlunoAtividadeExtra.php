<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunoAtividadeExtraRepository")
 * @ORM\Table(name="aluno_atividade_extra")
 */
class AlunoAtividadeExtra
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AtividadeExtra", inversedBy="alunoAtividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $atividade_extra;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoAtividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"P", "comment":"(P)resente, (F)altante, (R)eposição"})
     */
    private $presenca = 'P';

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $removido = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAtividadeExtra(): ?AtividadeExtra
    {
        return $this->atividade_extra;
    }

    public function setAtividadeExtra(?AtividadeExtra $atividade_extra): self
    {
        $this->atividade_extra = $atividade_extra;

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

    public function getPresenca(): ?string
    {
        return $this->presenca;
    }

    public function setPresenca(string $presenca): self
    {
        $this->presenca = $presenca;

        return $this;
    }

    public function getRemovido(): ?bool
    {
        return $this->removido;
    }

    public function setRemovido(bool $removido): self
    {
        $this->removido = $removido;

        return $this;
    }


}
