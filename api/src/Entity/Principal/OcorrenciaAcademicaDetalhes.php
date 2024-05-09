<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\OcorrenciaAcademicaDetalhesRepository")
 */
class OcorrenciaAcademicaDetalhes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\OcorrenciaAcademica", inversedBy="ocorrenciaAcademicaDetalhes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ocorrencia_academica;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TurmaAula", inversedBy="ocorrenciaAcademicaDetalhes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $turma_aula;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="text")
     */
    private $texto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="ocorrenciaAcademicaDetalhes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    public function __construct()
    {
        $this->data_criacao = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOcorrenciaAcademica(): ?OcorrenciaAcademica
    {
        return $this->ocorrencia_academica;
    }

    public function setOcorrenciaAcademica(?OcorrenciaAcademica $ocorrencia_academica): self
    {
        $this->ocorrencia_academica = $ocorrencia_academica;

        return $this;
    }

    public function getTurmaAula(): ?TurmaAula
    {
        return $this->turma_aula;
    }

    public function setTurmaAula(?TurmaAula $turma_aula): self
    {
        $this->turma_aula = $turma_aula;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

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
