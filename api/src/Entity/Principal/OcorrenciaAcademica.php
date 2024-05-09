<?php

namespace App\Entity\Principal;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\OcorrenciaAcademicaRepository")
 */
class OcorrenciaAcademica
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="ocorrenciaAcademicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="ocorrenciaAcademicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="ocorrenciaAcademicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoOcorrencia", inversedBy="ocorrenciaAcademicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo_ocorrencia;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_conclusao;

    /**
     * @ORM\Column(type="string", length=1,options={"default":"A","comment":"(A)berto, (F)echado"})
     */
    private $situacao = "A";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="ocorrenciaAcademicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademicaDetalhes", mappedBy="ocorrencia_academica")
     * @ORM\OrderBy({"data_criacao"="DESC"})
     */
    private $ocorrenciaAcademicaDetalhes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="ocorrencia_academica", cascade={"persist", "remove"})
     */
    private $reposicaoAula;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_proximo_contato;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="ocorrencia_academica")
     */
    private $agendaCompromissos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\OrigemOcorrencia", inversedBy="ocorrenciaAcademicas")
     */
    private $origem_ocorrencia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Contrato", inversedBy="ocorrenciaAcademicas")
     */
    private $contrato;


    public function __construct()
    {
        $this->data_criacao = new \DateTime();
        $this->ocorrenciaAcademicaDetalhes = new ArrayCollection();
        $this->agendaCompromissos          = new ArrayCollection();
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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

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

    public function getTipoOcorrencia(): ?TipoOcorrencia
    {
        return $this->tipo_ocorrencia;
    }

    public function setTipoOcorrencia(?TipoOcorrencia $tipo_ocorrencia): self
    {
        $this->tipo_ocorrencia = $tipo_ocorrencia;

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

    public function getDataConclusao(): ?\DateTimeInterface
    {
        return $this->data_conclusao;
    }

    public function setDataConclusao(?\DateTimeInterface $data_conclusao): self
    {
        $this->data_conclusao = $data_conclusao;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    /**
     * @return Collection|OcorrenciaAcademicaDetalhes[]
     */
    public function getOcorrenciaAcademicaDetalhes(): Collection
    {
        return $this->ocorrenciaAcademicaDetalhes;
    }

    public function addOcorrenciaAcademicaDetalhe(OcorrenciaAcademicaDetalhes $ocorrenciaAcademicaDetalhe): self
    {
        if ($this->ocorrenciaAcademicaDetalhes->contains($ocorrenciaAcademicaDetalhe) === false) {
            $this->ocorrenciaAcademicaDetalhes[] = $ocorrenciaAcademicaDetalhe;
            $ocorrenciaAcademicaDetalhe->setOcorrenciaAcademica($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademicaDetalhe(OcorrenciaAcademicaDetalhes $ocorrenciaAcademicaDetalhe): self
    {
        if ($this->ocorrenciaAcademicaDetalhes->contains($ocorrenciaAcademicaDetalhe) === true) {
            $this->ocorrenciaAcademicaDetalhes->removeElement($ocorrenciaAcademicaDetalhe);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademicaDetalhe->getOcorrenciaAcademica() === $this) {
                $ocorrenciaAcademicaDetalhe->setOcorrenciaAcademica(null);
            }
        }

        return $this;
    }

    public function getReposicaoAula(): ?ReposicaoAula
    {
        return $this->reposicaoAula;
    }

    public function setReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        $this->reposicaoAula = $reposicaoAula;

        // set the owning side of the relation if necessary
        if ($this !== $reposicaoAula->getOcorrenciaAcademica()) {
            $reposicaoAula->setOcorrenciaAcademica($this);
        }

        return $this;
    }

    public function getDataProximoContato(): ?\DateTimeInterface
    {
        return $this->data_proximo_contato;
    }

    public function setDataProximoContato(?\DateTimeInterface $data_proximo_contato): self
    {
        $this->data_proximo_contato = $data_proximo_contato;

        return $this;
    }

    /**
     * @return Collection|AgendaCompromisso[]
     */
    public function getAgendaCompromissos(): Collection
    {
        return $this->agendaCompromissos;
    }

    public function addAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === false) {
            $this->agendaCompromissos[] = $agendaCompromisso;
            $agendaCompromisso->setOcorrenciaAcademica($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getOcorrenciaAcademica() === $this) {
                $agendaCompromisso->setOcorrenciaAcademica(null);
            }
        }

        return $this;
    }

    public function getOrigemOcorrencia(): ?OrigemOcorrencia
    {
        return $this->origem_ocorrencia;
    }

    public function setOrigemOcorrencia(?OrigemOcorrencia $origem_ocorrencia): self
    {
        $this->origem_ocorrencia = $origem_ocorrencia;

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


}
