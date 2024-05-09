<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AlunoDiarioPersonalRepository")
 */
class AlunoDiarioPersonal
{


    public function __construct()
    {
        $this->data_criacao = new \DateTime();
        $this->aluno_diario_personal_licao   = new ArrayCollection();
        $this->pagamentoAlunoDiarioPersonals = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="alunoDiarioPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="alunoDiarioPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala_franqueada;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\AgendamentoPersonal", inversedBy="alunoDiarioPersonal", cascade={"persist", "remove"})
     */
    private $agendamento_personal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livro;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_aula;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $presenca;

    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"default":"E", "comment":"(E)ntregue, (EA)ntregue com Atraso, (NE)ão Entregue"})
     */
    private $atividade_ca;

    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"default":"E", "comment":"(E)ntregue, (EA)ntregue com Atraso, (NE)ão Entregue"})
     */
    private $atividade_ce;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Licao", inversedBy="alunoDiarioPersonals")
     */
    private $aluno_diario_personal_licao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\CreditosPersonal", inversedBy="alunoDiarioPersonals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creditos_personal;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoAlunoDiarioPersonal", mappedBy="aluno_diario_personal", orphanRemoval=true)
     */
    private $pagamentoAlunoDiarioPersonals;

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

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

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

    public function getSalaFranqueada(): ?SalaFranqueada
    {
        return $this->sala_franqueada;
    }

    public function setSalaFranqueada(?SalaFranqueada $sala_franqueada): self
    {
        $this->sala_franqueada = $sala_franqueada;

        return $this;
    }

    public function getAgendamentoPersonal(): ?AgendamentoPersonal
    {
        return $this->agendamento_personal;
    }

    public function setAgendamentoPersonal(?AgendamentoPersonal $agendamento_personal): self
    {
        $this->agendamento_personal = $agendamento_personal;

        return $this;
    }

    public function getLivro(): ?Livro
    {
        return $this->livro;
    }

    public function setLivro(?Livro $livro): self
    {
        $this->livro = $livro;

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

    public function getDataAula(): ?\DateTimeInterface
    {
        return clone $this->data_aula;
    }

    public function setDataAula(\DateTimeInterface $data_aula): self
    {
        $this->data_aula = clone $data_aula;

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

    public function getAtividadeCa(): ?string
    {
        return $this->atividade_ca;
    }

    public function setAtividadeCa(?string $atividade_ca): self
    {
        $this->atividade_ca = $atividade_ca;

        return $this;
    }

    public function getAtividadeCe(): ?string
    {
        return $this->atividade_ce;
    }

    public function setAtividadeCe(?string $atividade_ce): self
    {
        $this->atividade_ce = $atividade_ce;

        return $this;
    }

    /**
     * @return Collection|Licao[]
     */
    public function getAlunoDiarioPersonalLicao(): Collection
    {
        return $this->aluno_diario_personal_licao;
    }

    public function addAlunoDiarioPersonalLicao(Licao $alunoDiarioPersonalLicao): self
    {
        if ($this->aluno_diario_personal_licao->contains($alunoDiarioPersonalLicao) === false) {
            $this->aluno_diario_personal_licao[] = $alunoDiarioPersonalLicao;
        }

        return $this;
    }

    public function removeAlunoDiarioPersonalLicao(Licao $alunoDiarioPersonalLicao): self
    {
        if ($this->aluno_diario_personal_licao->contains($alunoDiarioPersonalLicao) === true) {
            $this->aluno_diario_personal_licao->removeElement($alunoDiarioPersonalLicao);
        }

        return $this;
    }

    public function getCreditosPersonal(): ?CreditosPersonal
    {
        return $this->creditos_personal;
    }

    public function setCreditosPersonal(?CreditosPersonal $creditos_personal): self
    {
        $this->creditos_personal = $creditos_personal;

        return $this;
    }

    /**
     * @return Collection|PagamentoAlunoDiarioPersonal[]
     */
    public function getPagamentoAlunoDiarioPersonals(): Collection
    {
        return $this->pagamentoAlunoDiarioPersonals;
    }

    public function addPagamentoAlunoDiarioPersonal(PagamentoAlunoDiarioPersonal $pagamentoAlunoDiarioPersonal): self
    {
        if ($this->pagamentoAlunoDiarioPersonals->contains($pagamentoAlunoDiarioPersonal) === false) {
            $this->pagamentoAlunoDiarioPersonals[] = $pagamentoAlunoDiarioPersonal;
            $pagamentoAlunoDiarioPersonal->setAlunoDiarioPersonal($this);
        }

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function removePagamentoAlunoDiarioPersonal(PagamentoAlunoDiarioPersonal $pagamentoAlunoDiarioPersonal): self
    {
        if ($this->pagamentoAlunoDiarioPersonals->contains($pagamentoAlunoDiarioPersonal) === true) {
            $this->pagamentoAlunoDiarioPersonals->removeElement($pagamentoAlunoDiarioPersonal);
            // set the owning side to null (unless already changed)
            if ($pagamentoAlunoDiarioPersonal->getAlunoDiarioPersonal() === $this) {
                $pagamentoAlunoDiarioPersonal->setAlunoDiarioPersonal(null);
            }
        }

        return $this;
    }


}
