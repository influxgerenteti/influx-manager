<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\InteressadoRepository")
 */
class Interessado
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="consultorInteressados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $consultor_funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="consultorResponsavelInteressados")
     */
    private $consultor_responsavel_funcionario;

    /**
     * @ORM\ManyToOne(targetEntity=Pessoa::class, inversedBy="interessados")
     */
    private $pessoa_indicou;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Workflow", inversedBy="interessados")
     */
    private $workflow;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="franqueadaInteressados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Idioma", inversedBy="interessados")
     */
    private $idiomas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="interessados")
     */
    private $aluno;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email_contato;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idade;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"A - Ativo, R - Receptivo"})
     */
    private $tipo_lead;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email_secundario;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_contato;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_secundario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_cadastro;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"M - Masculino, F - Feminino"})
     */
    private $sexo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="interessado")
     */
    private $followupComercials;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"default":"L","comment":"L - LEAD, I - INTERESSADO, H - HOTLIST"})
     */
    private $grau_interesse = 'L';

    /**
     * @ORM\Column(type="string", length=2, options={"comment":"A - Aberto, C - Convertido, I - Inativo, P - Perdido", "default":"I"})
     */
    private $situacao = 'I';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\MotivoNaoFechamentoMatricula", inversedBy="interessados")
     */
    private $motivo_nao_fechamento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaComercial", mappedBy="interessado")
     */
    private $agendaComerciais;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoContato", inversedBy="interessados")
     */
    private $tipo_contato;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoProspeccao", inversedBy="interessados")
     */
    private $tipo_prospeccao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\WorkflowAcao", inversedBy="interessados")
     */
    private $workflow_acao;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"M - Manhã, T - Tarde, N - Noite, S - Sábado"})
     */
    private $periodo_pretendido;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Curso", inversedBy="interessados")
     */
    private $curso;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_validade_promocao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_matricula_perdida;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\InteressadoAtividadeExtra", mappedBy="interessado")
     */
    private $interessadoAtividadeExtras;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_primeiro_atendimento;


    public function __construct()
    {
        $this->idiomas            = new ArrayCollection();
        $this->data_cadastro      = new \DateTime();
        $this->followupComercials = new ArrayCollection();
        $this->agendaComerciais   = new ArrayCollection();
        $this->interessadoAtividadeExtras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConsultorResponsavelFuncionario(): ?Funcionario
    {
        return $this->consultor_responsavel_funcionario;
    }

    public function setConsultorResponsavelFuncionario(?Funcionario $consultor_responsavel_funcionario): self
    {
        $this->consultor_responsavel_funcionario = $consultor_responsavel_funcionario;

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
     * @return Collection|Idioma[]
     */
    public function getIdiomas(): Collection
    {
        return $this->idiomas;
    }

    public function addIdioma(Idioma $idioma): self
    {
        if ($this->idiomas->contains($idioma) === false) {
            $this->idiomas[] = $idioma;
        }

        return $this;
    }

    public function removeIdioma(Idioma $idioma): self
    {
        if ($this->idiomas->contains($idioma) === true) {
            $this->idiomas->removeElement($idioma);
        }

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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEmailContato(): ?string
    {
        return $this->email_contato;
    }

    public function setEmailContato(?string $email_contato): self
    {
        $this->email_contato = $email_contato;

        return $this;
    }

    public function getIdade(): ?int
    {
        return $this->idade;
    }

    public function setIdade(?int $idade): self
    {
        $this->idade = $idade;

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

    public function getEmailSecundario(): ?string
    {
        return $this->email_secundario;
    }

    public function setEmailSecundario(?string $email_secundario): self
    {
        $this->email_secundario = $email_secundario;

        return $this;
    }

    public function getTelefoneContato(): ?string
    {
        return $this->telefone_contato;
    }

    public function setTelefoneContato(?string $telefone_contato): self
    {
        $this->telefone_contato = $telefone_contato;

        return $this;
    }

    public function getTelefoneSecundario(): ?string
    {
        return $this->telefone_secundario;
    }

    public function setTelefoneSecundario(?string $telefone_secundario): self
    {
        $this->telefone_secundario = $telefone_secundario;

        return $this;
    }

    public function getDataCadastro(): ?\DateTimeInterface
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(\DateTimeInterface $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(?string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * @return Collection|FollowupComercial[]
     */
    public function getFollowupComercials(): Collection
    {
        return $this->followupComercials;
    }

    public function addFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === false) {
            $this->followupComercials[] = $followupComercial;
            $followupComercial->setInteressado($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getInteressado() === $this) {
                $followupComercial->setInteressado(null);
            }
        }

        return $this;
    }

    public function getGrauInteresse(): ?string
    {
        return $this->grau_interesse;
    }

    public function setGrauInteresse(?string $grau_interesse): self
    {
        $this->grau_interesse = $grau_interesse;

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

    public function getMotivoNaoFechamento(): ?MotivoNaoFechamentoMatricula
    {
        return $this->motivo_nao_fechamento;
    }

    public function setMotivoNaoFechamento(?MotivoNaoFechamentoMatricula $motivo_nao_fechamento): self
    {
        $this->motivo_nao_fechamento = $motivo_nao_fechamento;

        return $this;
    }

    /**
     * @return Collection|AgendaComercial[]
     */
    public function getAgendaComerciais(): Collection
    {
        return $this->agendaComerciais;
    }

    public function addAgendaComerciai(AgendaComercial $agendaComerciai): self
    {
        if ($this->agendaComerciais->contains($agendaComerciai) === false) {
            $this->agendaComerciais[] = $agendaComerciai;
            $agendaComerciai->setInteressado($this);
        }

        return $this;
    }

    public function removeAgendaComerciai(AgendaComercial $agendaComerciai): self
    {
        if ($this->agendaComerciais->contains($agendaComerciai) === true) {
            $this->agendaComerciais->removeElement($agendaComerciai);
            // set the owning side to null (unless already changed)
            if ($agendaComerciai->getInteressado() === $this) {
                $agendaComerciai->setInteressado(null);
            }
        }

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

    public function getWorkflowAcao(): ?WorkflowAcao
    {
        return $this->workflow_acao;
    }

    public function setWorkflowAcao(?WorkflowAcao $workflow_acao): self
    {
        $this->workflow_acao = $workflow_acao;

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

    public function getPeriodoPretendido(): ?string
    {
        return $this->periodo_pretendido;
    }

    public function setPeriodoPretendido(?string $periodo_pretendido): self
    {
        $this->periodo_pretendido = $periodo_pretendido;

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

    public function getDataMatriculaPerdida(): ?\DateTimeInterface
    {
        return $this->data_matricula_perdida;
    }

    public function setDataMatriculaPerdida(?\DateTimeInterface $data_matricula_perdida): self
    {
        $this->data_matricula_perdida = $data_matricula_perdida;

        return $this;
    }

    /**
     * @return Collection|InteressadoAtividadeExtra[]
     */
    public function getInteressadoAtividadeExtras(): Collection
    {
        return $this->interessadoAtividadeExtras;
    }

    public function addInteressadoAtividadeExtra(InteressadoAtividadeExtra $interessadoAtividadeExtra): self
    {
        if ($this->interessadoAtividadeExtras->contains($interessadoAtividadeExtra) === false) {
            $this->interessadoAtividadeExtras[] = $interessadoAtividadeExtra;
            $interessadoAtividadeExtra->setInteressado($this);
        }

        return $this;
    }

    public function removeInteressadoAtividadeExtra(InteressadoAtividadeExtra $interessadoAtividadeExtra): self
    {
        if ($this->interessadoAtividadeExtras->contains($interessadoAtividadeExtra) === true) {
            $this->interessadoAtividadeExtras->removeElement($interessadoAtividadeExtra);
            // set the owning side to null (unless already changed)
            if ($interessadoAtividadeExtra->getInteressado() === $this) {
                $interessadoAtividadeExtra->setInteressado(null);
            }
        }

        return $this;
    }

    public function getDataPrimeiroAtendimento(): ?\DateTimeInterface
    {
        return $this->data_primeiro_atendimento;
    }

    public function setDataPrimeiroAtendimento(?\DateTimeInterface $data_primeiro_atendimento): self
    {
        $this->data_primeiro_atendimento = $data_primeiro_atendimento;

        return $this;
    }

    public function getPessoaIndicou(): ?Pessoa
    {
        return $this->pessoa_indicou;
    }

    public function setPessoaIndicou(?Pessoa $pessoa_indicou): self
    {
        $this->pessoa_indicou = $pessoa_indicou;

        return $this;
    }


}
