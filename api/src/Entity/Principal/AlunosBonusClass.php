<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunosBonusClassRepository")
 */
class AlunosBonusClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $horario_aula;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    private $conteudo;

    /**
     *
     * @ORM\Column(type="string", length=1, options={"comment":"(P)RESENTE, (F)ALTA", "default":"P"})
     */
    private $presenca = 'P';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunosBonusClasses")
     * @ORM\JoinColumn(nullable=true)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\BonusClass", inversedBy="alunosBonusClasses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bonus_class;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $selecionado;

    public function __construct()
    {
        $this->aluno       = new ArrayCollection();
        $this->bonus_class = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHorarioAula(): ?\DateTimeInterface
    {
        return $this->horario_aula;
    }

    public function setHorarioAula(\DateTimeInterface $horario_aula): self
    {
        $this->horario_aula = $horario_aula;

        return $this;
    }

    public function getConteudo(): ?string
    {
        return $this->conteudo;
    }

    public function setConteudo(?string $conteudo): self
    {
        $this->conteudo = $conteudo;

        return $this;
    }

    public function getPresenca(): ?string
    {
        return $this->presenca;
    }

    public function setPresenca(?string $presenca): self
    {
        $this->presenca = $presenca;

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

    public function getBonusClass(): ?BonusClass
    {
        return $this->bonus_class;
    }

    public function setBonusClass(?BonusClass $bonus_class): self
    {
        $this->bonus_class = $bonus_class;

        return $this;
    }

    public function getSelecionado(): ?bool
    {
        return $this->selecionado;
    }

    public function setSelecionado(?bool $selecionado): self
    {
        $this->selecionado = $selecionado;

        return $this;
    }


}
