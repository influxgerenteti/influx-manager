<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ConvenioRepository")
 * @ORM\Table(name="convenio",
 *    uniqueConstraints={
 * @ORM\UniqueConstraint(name="indice_relacional",
 *            columns={"pessoa_id"})
 *    })
 */
class Convenio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="convenios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa", inversedBy="convenios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pessoa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\EtapasConvenio", inversedBy="convenios")
     */
    private $etapas_convenio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\SegmentoEmpresaConvenio", inversedBy="convenios")
     */
    private $segmento_empresa_convenio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\MotivoNaoFechamentoConvenio", inversedBy="convenios")
     */
    private $motivo_nao_fechamento_convenio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="convenios")
     */
    private $consultor_funcionario;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"A - Ativo, I - Inativo, PV - Pendente Validação Franqueadora, EN - Em Negociação, NE - Negado, RF - Retornar Futuramente, SC - Sem Convênio","default":"PV"})
     */
    private $situacao = 'EN';

    /**
     * @ORM\Column(type="boolean", options={"comment":"false - cidade, true  - nacional"}, nullable=true,)
     */
    private $abrangencia_nacional = false;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $nome_contato;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email_contato;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $telefone_contato;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_contato_secundario;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $beneficiario_colaboradores;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $beneficiario_dependentes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $beneficiario_associados;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $beneficiario_alunos;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $beneficiario_estagiarios;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $beneficiario_terceiros;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Caminho do arquivo do contrato"})
     */
    private $contrato_digitalizado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_cadastro;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_avaliacao_franqueadora;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $justificativa_franqueadora;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_proximo_contato;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $horario_proximo_contato;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupConvenio", mappedBy="convenio")
     */
    private $followupConvenios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="convenio_desconto")
     */
    private $contratos;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_primeiro_atendimento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\NegociacaoParceriaWorkflow", inversedBy="convenios")
     */
    private $negociacao_parceria_workflow;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inscricao_municipal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inscricao_estadual;

    public function __construct()
    {
        $this->data_cadastro     = new \DateTime();
        $this->followupConvenios = new ArrayCollection();
        $this->contratos         = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(?Pessoa $pessoa): self
    {
        $this->pessoa = $pessoa;

        return $this;
    }

    public function getEtapasConvenio(): ?EtapasConvenio
    {
        return $this->etapas_convenio;
    }

    public function setEtapasConvenio(?EtapasConvenio $etapas_convenio): self
    {
        $this->etapas_convenio = $etapas_convenio;

        return $this;
    }

    public function getSegmentoEmpresaConvenio(): ?SegmentoEmpresaConvenio
    {
        return $this->segmento_empresa_convenio;
    }

    public function setSegmentoEmpresaConvenio(?SegmentoEmpresaConvenio $segmento_empresa_convenio): self
    {
        $this->segmento_empresa_convenio = $segmento_empresa_convenio;

        return $this;
    }

    public function getMotivoNaoFechamentoConvenio(): ?MotivoNaoFechamentoConvenio
    {
        return $this->motivo_nao_fechamento_convenio;
    }

    public function setMotivoNaoFechamentoConvenio(?MotivoNaoFechamentoConvenio $motivo_nao_fechamento_convenio): self
    {
        $this->motivo_nao_fechamento_convenio = $motivo_nao_fechamento_convenio;

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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getAbrangenciaNacional(): ?bool
    {
        return $this->abrangencia_nacional;
    }

    public function setAbrangenciaNacional(bool $abrangencia_nacional): self
    {
        $this->abrangencia_nacional = $abrangencia_nacional;

        return $this;
    }

    public function getNomeContato(): ?string
    {
        return $this->nome_contato;
    }

    public function setNomeContato(string $nome_contato): self
    {
        $this->nome_contato = $nome_contato;

        return $this;
    }

    public function getEmailContato(): ?string
    {
        return $this->email_contato;
    }

    public function setEmailContato(string $email_contato): self
    {
        $this->email_contato = $email_contato;

        return $this;
    }

    public function getTelefoneContato(): ?string
    {
        return $this->telefone_contato;
    }

    public function setTelefoneContato(string $telefone_contato): self
    {
        $this->telefone_contato = $telefone_contato;

        return $this;
    }

    public function getTelefoneContatoSecundario(): ?string
    {
        return $this->telefone_contato_secundario;
    }

    public function setTelefoneContatoSecundario(?string $telefone_contato_secundario): self
    {
        $this->telefone_contato_secundario = $telefone_contato_secundario;

        return $this;
    }

    public function getBeneficiarioColaboradores(): ?bool
    {
        return $this->beneficiario_colaboradores;
    }

    public function setBeneficiarioColaboradores(?bool $beneficiario_colaboradores): self
    {
        $this->beneficiario_colaboradores = $beneficiario_colaboradores;

        return $this;
    }

    public function getBeneficiarioDependentes(): ?bool
    {
        return $this->beneficiario_dependentes;
    }

    public function setBeneficiarioDependentes(?bool $beneficiario_dependentes): self
    {
        $this->beneficiario_dependentes = $beneficiario_dependentes;

        return $this;
    }

    public function getBeneficiarioAssociados(): ?bool
    {
        return $this->beneficiario_associados;
    }

    public function setBeneficiarioAssociados(?bool $beneficiario_associados): self
    {
        $this->beneficiario_associados = $beneficiario_associados;

        return $this;
    }

    public function getBeneficiarioAlunos(): ?bool
    {
        return $this->beneficiario_alunos;
    }

    public function setBeneficiarioAlunos(?bool $beneficiario_alunos): self
    {
        $this->beneficiario_alunos = $beneficiario_alunos;

        return $this;
    }

    public function getBeneficiarioEstagiarios(): ?bool
    {
        return $this->beneficiario_estagiarios;
    }

    public function setBeneficiarioEstagiarios(?bool $beneficiario_estagiarios): self
    {
        $this->beneficiario_estagiarios = $beneficiario_estagiarios;

        return $this;
    }

    public function getBeneficiarioTerceiros(): ?bool
    {
        return $this->beneficiario_terceiros;
    }

    public function setBeneficiarioTerceiros(?bool $beneficiario_terceiros): self
    {
        $this->beneficiario_terceiros = $beneficiario_terceiros;

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

    public function getContratoDigitalizado(): ?string
    {
        return $this->contrato_digitalizado;
    }

    public function setContratoDigitalizado(?string $contrato_digitalizado): self
    {
        $this->contrato_digitalizado = $contrato_digitalizado;

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

    public function getDataAvaliacaoFranqueadora(): ?\DateTimeInterface
    {
        return $this->data_avaliacao_franqueadora;
    }

    public function setDataAvaliacaoFranqueadora(?\DateTimeInterface $data_avaliacao_franqueadora): self
    {
        $this->data_avaliacao_franqueadora = $data_avaliacao_franqueadora;

        return $this;
    }

    public function getJustificativaFranqueadora(): ?string
    {
        return $this->justificativa_franqueadora;
    }

    public function setJustificativaFranqueadora(?string $justificativa_franqueadora): self
    {
        $this->justificativa_franqueadora = $justificativa_franqueadora;

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

    public function getHorarioProximoContato(): ?\DateTimeInterface
    {
        return $this->horario_proximo_contato;
    }

    public function setHorarioProximoContato(?\DateTimeInterface $horario_proximo_contato): self
    {
        $this->horario_proximo_contato = $horario_proximo_contato;

        return $this;
    }

    /**
     * @return Collection|FollowupConvenio[]
     */
    public function getFollowupConvenios(): Collection
    {
        return $this->followupConvenios;
    }

    public function addFollowupConvenio(FollowupConvenio $followupConvenio): self
    {
        if ($this->followupConvenios->contains($followupConvenio) === false) {
            $this->followupConvenios[] = $followupConvenio;
            $followupConvenio->setConvenio($this);
        }

        return $this;
    }

    public function removeFollowupConvenio(FollowupConvenio $followupConvenio): self
    {
        if ($this->followupConvenios->contains($followupConvenio) === true) {
            $this->followupConvenios->removeElement($followupConvenio);
            // set the owning side to null (unless already changed)
            if ($followupConvenio->getConvenio() === $this) {
                $followupConvenio->setConvenio(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    public function addContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === false) {
            $this->contratos[] = $contrato;
            $contrato->setConvenioDesconto($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getConvenioDesconto() === $this) {
                $contrato->setConvenioDesconto(null);
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

    public function getNegociacaoParceriaWorkflow(): ?NegociacaoParceriaWorkflow
    {
        return $this->negociacao_parceria_workflow;
    }

    public function setNegociacaoParceriaWorkflow(?NegociacaoParceriaWorkflow $negociacao_parceria_workflow): self
    {
        $this->negociacao_parceria_workflow = $negociacao_parceria_workflow;

        return $this;
    }

    public function getInscricaoMunicipal(): ?string
    {
        return $this->inscricao_municipal;
    }

    public function setInscricaoMunicipal(?string $inscricao_municipal): self
    {
        $this->inscricao_municipal = $inscricao_municipal;

        return $this;
    }

    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricao_estadual;
    }

    public function setInscricaoEstadual(?string $inscricao_estadual): self
    {
        $this->inscricao_estadual = $inscricao_estadual;

        return $this;
    }


}
