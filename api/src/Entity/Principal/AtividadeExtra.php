<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AtividadeExtraRepository")
 * @ORM\Table(name="atividade_extra")
 */
class AtividadeExtra
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item", inversedBy="atividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="atividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario_solicitante;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="atividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SalaFranqueada", inversedBy="atividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala_franqueada;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descricao_atividade;

    /**
     * @ORM\Column(type="string", length=2, options={"default":"P", "comment":"P - Pendente, C - Concluido, CC - Cancelado"})
     */
    private $situacao = 'P';

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_hora_inicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_hora_fim;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AlunoAtividadeExtra", mappedBy="atividade_extra")
     */
    private $alunoAtividadeExtras;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Funcionario", inversedBy="atividadeExtrasPendentes")
     */
    private $responsaveis_execucacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento", inversedBy="atividadeExtras")
     */
    private $forma_cobranca;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\InteressadoAtividadeExtra", mappedBy="atividade_extra", cascade={"persist", "remove"})
     */
    private $interessadoAtividadeExtra;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\ContaReceber", inversedBy="atividadeExtras")
     */
    private $conta_receber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantidade_maxima_alunos;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isenta;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="atividade_extra")
     */
    private $agendaCompromissos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\PagamentoAtividadeExtra", mappedBy="atividade_extra", orphanRemoval=true)
     */
    private $pagamentoAtividadeExtras;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ConvidadoAtividadeExtra", mappedBy="atividade_extra")
     */
    private $convidadoAtividadeExtras;

    public function __construct()
    {
        $this->alunoAtividadeExtras    = new ArrayCollection();
        $this->responsaveis_execucacao = new ArrayCollection();
        $this->data_criacao            = new \DateTime();
        $this->conta_receber           = new ArrayCollection();
        $this->agendaCompromissos      = new ArrayCollection();
        $this->pagamentoAtividadeExtras = new ArrayCollection();
        $this->convidadoAtividadeExtras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getUsuarioSolicitante(): ?Usuario
    {
        return $this->usuario_solicitante;
    }

    public function setUsuarioSolicitante(?Usuario $usuario_solicitante): self
    {
        $this->usuario_solicitante = $usuario_solicitante;

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

    public function getDescricaoAtividade(): ?string
    {
        return $this->descricao_atividade;
    }

    public function setDescricaoAtividade(?string $descricao_atividade): self
    {
        $this->descricao_atividade = $descricao_atividade;

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

    public function getDataHoraInicio(): ?\DateTimeInterface
    {
        return $this->data_hora_inicio;
    }

    public function setDataHoraInicio(\DateTimeInterface $data_hora_inicio): self
    {
        $this->data_hora_inicio = $data_hora_inicio;

        return $this;
    }

    public function getDataHoraFim(): ?\DateTimeInterface
    {
        return $this->data_hora_fim;
    }

    public function setDataHoraFim(\DateTimeInterface $data_hora_fim): self
    {
        $this->data_hora_fim = $data_hora_fim;

        return $this;
    }

    /**
     * @return Collection|AlunoAtividadeExtra[]
     */
    public function getAlunoAtividadeExtras(): Collection
    {
        return $this->alunoAtividadeExtras;
    }

    public function addAlunoAtividadeExtra(AlunoAtividadeExtra $alunoAtividadeExtra): self
    {
        if ($this->alunoAtividadeExtras->contains($alunoAtividadeExtra) === false) {
            $this->alunoAtividadeExtras[] = $alunoAtividadeExtra;
            $alunoAtividadeExtra->setAtividadeExtra($this);
        }

        return $this;
    }

    public function removeAlunoAtividadeExtra(AlunoAtividadeExtra $alunoAtividadeExtra): self
    {
        if ($this->alunoAtividadeExtras->contains($alunoAtividadeExtra) === true) {
            $this->alunoAtividadeExtras->removeElement($alunoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($alunoAtividadeExtra->getAtividadeExtra() === $this) {
                $alunoAtividadeExtra->setAtividadeExtra(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Funcionario[]
     */
    public function getResponsaveisExecucacao(): Collection
    {
        return $this->responsaveis_execucacao;
    }

    public function addResponsaveisExecucacao(Funcionario $responsaveisExecucacao): self
    {
        if ($this->responsaveis_execucacao->contains($responsaveisExecucacao) === false) {
            $this->responsaveis_execucacao[] = $responsaveisExecucacao;
        }

        return $this;
    }

    public function removeResponsaveisExecucacao(Funcionario $responsaveisExecucacao): self
    {
        if ($this->responsaveis_execucacao->contains($responsaveisExecucacao) === true) {
            $this->responsaveis_execucacao->removeElement($responsaveisExecucacao);
        }

        return $this;
    }

    public function getFormaCobranca(): ?FormaPagamento
    {
        return $this->forma_cobranca;
    }

    public function setFormaCobranca(?FormaPagamento $forma_cobranca): self
    {
        $this->forma_cobranca = $forma_cobranca;

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

    public function getInteressadoAtividadeExtra(): ?InteressadoAtividadeExtra
    {
        return $this->interessadoAtividadeExtra;
    }

    public function setInteressadoAtividadeExtra(InteressadoAtividadeExtra $interessadoAtividadeExtra): self
    {
        $this->interessadoAtividadeExtra = $interessadoAtividadeExtra;

        // set the owning side of the relation if necessary
        if ($this !== $interessadoAtividadeExtra->getAtividadeExtra()) {
            $interessadoAtividadeExtra->setAtividadeExtra($this);
        }

        return $this;
    }

    /**
     * @return Collection|ContaReceber[]
     */
    public function getContaReceber(): Collection
    {
        return $this->conta_receber;
    }

    public function addContaReceber(ContaReceber $contaReceber): self
    {
        if ($this->conta_receber->contains($contaReceber) === false) {
            $this->conta_receber[] = $contaReceber;
        }

        return $this;
    }

    public function removeContaReceber(ContaReceber $contaReceber): self
    {
        if ($this->conta_receber->contains($contaReceber) === true) {
            $this->conta_receber->removeElement($contaReceber);
        }

        return $this;
    }

    public function getQuantidadeMaximaAlunos(): ?int
    {
        return $this->quantidade_maxima_alunos;
    }

    public function setQuantidadeMaximaAlunos(?int $quantidade_maxima_alunos): self
    {
        $this->quantidade_maxima_alunos = $quantidade_maxima_alunos;

        return $this;
    }

    public function getIsenta(): ?bool
    {
        return $this->isenta;
    }

    public function setIsenta(?bool $isenta): self
    {
        $this->isenta = $isenta;

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
            $agendaCompromisso->setAtividadeExtra($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getAtividadeExtra() === $this) {
                $agendaCompromisso->setAtividadeExtra(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PagamentoAtividadeExtra[]
     */
    public function getPagamentoAtividadeExtras(): Collection
    {
        return $this->pagamentoAtividadeExtras;
    }

    public function addPagamentoAtividadeExtra(PagamentoAtividadeExtra $pagamentoAtividadeExtra): self
    {
        if ($this->pagamentoAtividadeExtras->contains($pagamentoAtividadeExtra) === false) {
            $this->pagamentoAtividadeExtras[] = $pagamentoAtividadeExtra;
            $pagamentoAtividadeExtra->setAtividadeExtra($this);
        }

        return $this;
    }

    public function removePagamentoAtividadeExtra(PagamentoAtividadeExtra $pagamentoAtividadeExtra): self
    {
        if ($this->pagamentoAtividadeExtras->contains($pagamentoAtividadeExtra) === true) {
            $this->pagamentoAtividadeExtras->removeElement($pagamentoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($pagamentoAtividadeExtra->getAtividadeExtra() === $this) {
                $pagamentoAtividadeExtra->setAtividadeExtra(null);
            }
        }

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * @return Collection|ConvidadoAtividadeExtra[]
     */
    public function getConvidadoAtividadeExtras(): Collection
    {
        return $this->convidadoAtividadeExtras;
    }

    public function addConvidadoAtividadeExtra(ConvidadoAtividadeExtra $convidadoAtividadeExtra): self
    {
        if ($this->convidadoAtividadeExtras->contains($convidadoAtividadeExtra) === false) {
            $this->convidadoAtividadeExtras[] = $convidadoAtividadeExtra;
            $convidadoAtividadeExtra->setAtividadeExtra($this);
        }

        return $this;
    }

    public function removeConvidadoAtividadeExtra(ConvidadoAtividadeExtra $convidadoAtividadeExtra): self
    {
        if ($this->convidadoAtividadeExtras->contains($convidadoAtividadeExtra) === true) {
            $this->convidadoAtividadeExtras->removeElement($convidadoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($convidadoAtividadeExtra->getAtividadeExtra() === $this) {
                $convidadoAtividadeExtra->setAtividadeExtra(null);
            }
        }

        return $this;
    }


}
