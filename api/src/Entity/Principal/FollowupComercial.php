<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FollowupComercialRepository")
 * @ORM\Table(name="followup_comercial")
 */
class FollowupComercial
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Interessado", inversedBy="followupComercials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $interessado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="followupComercials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoContato", inversedBy="followupComercials")
     */
    private $tipo_contato;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_registro;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $followup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AgendaComercial", inversedBy="followupComercials")
     */
    private $agenda_comercial;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"L - LEAD, I - INTERESSADO, H - HOTLIST"})
     */
    private $grau_interesse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Curso", inversedBy="followupComercials")
     */
    private $curso;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_validade_promocao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Workflow", inversedBy="followupComercials")
     */
    private $workflow;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\WorkflowAcao", inversedBy="followupComercials")
     */
    private $workflow_acao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\MotivoNaoFechamentoMatricula", inversedBy="followupComercials")
     */
    private $motivo_nao_fechamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoProspeccao", inversedBy="followupComercials")
     */
    private $tipo_prospeccao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="followupComercials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $consultor_funcionario;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $tipo_lead;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"M - Manhã, T - Tarde, N - Noite, S - Sábado"})
     */
    private $periodo_pretendido;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_matricula_perdida;

    public function __construct()
    {
        $this->data_registro = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInteressado(): ?Interessado
    {
        return $this->interessado;
    }

    public function setInteressado(?Interessado $interessado): self
    {
        $this->interessado = $interessado;

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

    public function getTipoContato(): ?TipoContato
    {
        return $this->tipo_contato;
    }

    public function setTipoContato(?TipoContato $tipo_contato): self
    {
        $this->tipo_contato = $tipo_contato;

        return $this;
    }

    public function getDataRegistro(): ?\DateTimeInterface
    {
        return $this->data_registro;
    }

    public function setDataRegistro(\DateTimeInterface $data_registro): self
    {
        $this->data_registro = $data_registro;

        return $this;
    }

    public function getFollowup(): string
    {
        return $this->followup;
    }

    public function setFollowup(string $followup): self
    {
        $this->followup = $followup;

        return $this;
    }

    public function getAgendaComercial(): ?AgendaComercial
    {
        return $this->agenda_comercial;
    }

    public function setAgendaComercial(?AgendaComercial $agenda_comercial): self
    {
        $this->agenda_comercial = $agenda_comercial;

        return $this;
    }

    public function getGrauInteresse(): ?string
    {
        return $this->grau_interesse;
    }

    public function setGrauInteresse(string $grau_interesse): self
    {
        $this->grau_interesse = $grau_interesse;

        return $this;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

        return $this;
    }

    public function getDataValidadePromocao(): ?\DateTimeInterface
    {
        return $this->data_validade_promocao;
    }

    public function setDataValidadePromocao(?\DateTimeInterface $data_validade_promocao): self
    {
        $this->data_validade_promocao = $data_validade_promocao;

        return $this;
    }

    public function getWorkflow(): ?Workflow
    {
        return $this->workflow;
    }

    public function setWorkflow(?Workflow $workflow): self
    {
        $this->workflow = $workflow;

        return $this;
    }

    public function getWorkflowAcao(): ?WorkflowAcao
    {
        return $this->workflow_acao;
    }

    public function setWorkflowAcao(?WorkflowAcao $workflow_acao): self
    {
        $this->workflow_acao = $workflow_acao;

        return $this;
    }

    public function getMotivoNaoFechamento(): ?MotivoNaoFechamentoMatricula
    {
        return $this->motivo_nao_fechamento;
    }

    public function setMotivoNaoFechamento(?MotivoNaoFechamentoMatricula $motivo_nao_fechamento): self
    {
        $this->motivo_nao_fechamento = $motivo_nao_fechamento;

        return $this;
    }

    public function getTipoProspeccao(): ?TipoProspeccao
    {
        return $this->tipo_prospeccao;
    }

    public function setTipoProspeccao(?TipoProspeccao $tipo_prospeccao): self
    {
        $this->tipo_prospeccao = $tipo_prospeccao;

        return $this;
    }

    public function getConsultorFuncionario(): ?Funcionario
    {
        return $this->consultor_funcionario;
    }

    public function setConsultorFuncionario(?Funcionario $consultor_funcionario): self
    {
        $this->consultor_funcionario = $consultor_funcionario;

        return $this;
    }

    public function getTipoLead(): ?string
    {
        return $this->tipo_lead;
    }

    public function setTipoLead(?string $tipo_lead): self
    {
        $this->tipo_lead = $tipo_lead;

        return $this;
    }

    public function getPeriodoPretendido(): ?string
    {
        return $this->periodo_pretendido;
    }

    public function setPeriodoPretendido(?string $periodo_pretendido): self
    {
        $this->periodo_pretendido = $periodo_pretendido;

        return $this;
    }

    public function getDataMatriculaPerdida(): ?\DateTimeInterface
    {
        return $this->data_matricula_perdida;
    }

    public function setDataMatriculaPerdida(?\DateTimeInterface $data_matricula_perdida): self
    {
        $this->data_matricula_perdida = $data_matricula_perdida;

        return $this;
    }


}
