<?php

namespace App\Entity\Principal;

use Carbon\Traits\ToStringFormat;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PessoaRepository")
 * @ORM\Table(name="pessoa",
 *    uniqueConstraints={
 * @ORM\UniqueConstraint(name="indice_relacional",
 *            columns={"cnpj_cpf"})
 *    })
 */
class Pessoa
{


    public function __construct()
    {
        $this->data_cadastramento = new \DateTime();
        $this->franqueadas        = new ArrayCollection();
        $this->chequesPessoa      = new ArrayCollection();
        $this->contratos          = new ArrayCollection();
        $this->pessoaContaReceber = new ArrayCollection();
        $this->tituloRecebers     = new ArrayCollection();
        $this->convenios          = new ArrayCollection();
        $this->interessados       = new ArrayCollection();
    }

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\ManyToMany(targetEntity="\App\Entity\Principal\Franqueada", inversedBy="pessoasFranqueada")
     * @ORM\JoinTable(name="pessoa_franqueada",                         joinColumns={
     *   @ORM\JoinColumn(name="pessoa_id",                                referencedColumnName="id")
     * },
     * inverseJoinColumns={
     *   @ORM\JoinColumn(name="franqueada_id",                            referencedColumnName="id")
     * })
     */
    private $franqueadas;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Aluno", mappedBy="pessoa", orphanRemoval=true)
     */
    private $alunos;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Funcionario", mappedBy="pessoa", orphanRemoval=true)
     */
    private $funcionarios;

    /**
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $cnpj_cpf;

    /**
     *
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $razao_social;

    /**
     *
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $nome_fantasia;

    /**
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numero_identidade;

    /**
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $orgao_emissor;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_cadastramento;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_consulta_credito;

    /**
     *
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"M - Masculino, F - Feminino, O - Outro, N - Não informado", "default":"N"})
     */
    private $sexo = 'N';

    /**
     *
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"S - Solteiro, C - Casado, D - Divorciado"})
     */
    private $estado_civil;

    /**
     *
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $excluido = false;

    /**
     *
     * @ORM\Column(type="string", length=1, nullable=true,  options={"comment":"J - Jurídica, F - Física", "default":"F"})
     */
    private $tipo_pessoa = 'F';

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $inscricao_estadual;

    /**
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $inscricao_municipal;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nome_contato;

    /**
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $home_page;

    /**
     *
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $endereco;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero_endereco;

    /**
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $bairro_endereco;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complemento_endereco;

    /**
     *
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $cep_endereco;

    /**
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email_preferencial;

    /**
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email_contato;

    /**
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email_profissional;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_preferencial;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_contato;

    /**
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefone_profissional;

    /**
     *
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $negativado = false;

    /**
     *
     * @ORM\Column(type="integer", nullable=true, options={"comment":"Código sistema antigo"})
     */
    private $id_importado;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Estado", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $estado;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Cidade", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $cidade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Banco", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $banco;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\PlanoConta", inversedBy="pessoas")
     * @ORM\JoinColumn(nullable=true)
     */
    private $plano_conta;

    /**
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $agencia;

    /**
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $conta;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_nascimento;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cheque", mappedBy="pessoa")
     */
    private $chequesPessoa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Contrato", mappedBy="responsavel_financeiro_pessoa")
     */
    private $contratos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaReceber", mappedBy="sacado_pessoa")
     */
    private $pessoaContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloReceber", mappedBy="sacado_pessoa")
     */
    private $tituloRecebers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Convenio", mappedBy="pessoa")
     */
    private $convenios;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inadimplente = false;

    /**
     * @ORM\OneToMany(targetEntity=Interessado::class, mappedBy="pessoa_indicou")
     */
    private $interessados;



    /**
     * @var                       string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sponte_id;

    
    
    public function __toString(){
        return "teste";
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getCnpjCpf(): ?string
    {
        return $this->cnpj_cpf;
    }

    public function setCnpjCpf(?string $cnpj_cpf): self
    {
        $this->cnpj_cpf = $cnpj_cpf;

        return $this;
    }

    public function getRazaoSocial(): ?string
    {
        return $this->razao_social;
    }

    public function setRazaoSocial(?string $razao_social): self
    {
        $this->razao_social = $razao_social;

        return $this;
    }

    public function getNomeFantasia(): ?string
    {
        return $this->nome_fantasia;
    }

    public function setNomeFantasia(?string $nome_fantasia): self
    {
        $this->nome_fantasia = $nome_fantasia;

        return $this;
    }

    public function getNumeroIdentidade(): ?string
    {
        return $this->numero_identidade;
    }

    public function setNumeroIdentidade(string $numero_identidade): self
    {
        $this->numero_identidade = $numero_identidade;

        return $this;
    }

    public function getOrgaoEmissor(): ?string
    {
        return $this->orgao_emissor;
    }

    public function setOrgaoEmissor(string $orgao_emissor): self
    {
        $this->orgao_emissor = $orgao_emissor;

        return $this;
    }

    public function getDataCadastramento(): ?\DateTimeInterface
    {
        return $this->data_cadastramento;
    }

    public function setDataCadastramento(\DateTimeInterface $data_cadastramento): self
    {
        $this->data_cadastramento = $data_cadastramento;

        return $this;
    }

    public function getDataConsultaCredito(): ?\DateTimeInterface
    {
        return $this->data_consulta_credito;
    }

    public function setDataConsultaCredito(?\DateTimeInterface $data_consulta_credito): self
    {
        $this->data_consulta_credito = $data_consulta_credito;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getEstadoCivil(): ?string
    {
        return $this->estado_civil;
    }

    public function setEstadoCivil(string $estado_civil): self
    {
        $this->estado_civil = $estado_civil;

        return $this;
    }

    public function getExcluido(): ?bool
    {
        return $this->excluido;
    }

    public function setExcluido(bool $excluido): self
    {
        $this->excluido = $excluido;

        return $this;
    }

    public function getTipoPessoa(): ?string
    {
        return $this->tipo_pessoa;
    }

    public function setTipoPessoa(string $tipo_pessoa): self
    {
        $this->tipo_pessoa = $tipo_pessoa;

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricao_estadual;
    }

    public function setInscricaoEstadual(string $inscricao_estadual): self
    {
        $this->inscricao_estadual = $inscricao_estadual;

        return $this;
    }

    public function getInscricaoMunicipal(): ?string
    {
        return $this->inscricao_municipal;
    }

    public function setInscricaoMunicipal(string $inscricao_municipal): self
    {
        $this->inscricao_municipal = $inscricao_municipal;

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

    public function getHomePage(): ?string
    {
        return $this->home_page;
    }

    public function setHomePage(string $home_page): self
    {
        $this->home_page = $home_page;

        return $this;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function setEndereco(string $endereco): self
    {
        $this->endereco = $endereco;

        return $this;
    }

    public function getNumeroEndereco(): ?int
    {
        return $this->numero_endereco;
    }

    public function setNumeroEndereco(int $numero_endereco): self
    {
        $this->numero_endereco = $numero_endereco;

        return $this;
    }

    public function getBairroEndereco(): ?string
    {
        return $this->bairro_endereco;
    }

    public function setBairroEndereco(string $bairro_endereco): self
    {
        $this->bairro_endereco = $bairro_endereco;

        return $this;
    }

    public function getComplementoEndereco(): ?string
    {
        return $this->complemento_endereco;
    }

    public function setComplementoEndereco(string $complemento_endereco): self
    {
        $this->complemento_endereco = $complemento_endereco;

        return $this;
    }

    public function getCepEndereco(): ?string
    {
        return $this->cep_endereco;
    }

    public function setCepEndereco(string $cep_endereco): self
    {
        $this->cep_endereco = $cep_endereco;

        return $this;
    }

    public function getEmailPreferencial(): ?string
    {
        return $this->email_preferencial;
    }

    public function setEmailPreferencial(string $email_preferencial): self
    {
        $this->email_preferencial = $email_preferencial;

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

    public function getEmailProfissional(): ?string
    {
        return $this->email_profissional;
    }

    public function setEmailProfissional(string $email_profissional): self
    {
        $this->email_profissional = $email_profissional;

        return $this;
    }

    public function getTelefonePreferencial(): ?string
    {
        return $this->telefone_preferencial;
    }

    public function setTelefonePreferencial(string $telefone_preferencial): self
    {
        $this->telefone_preferencial = $telefone_preferencial;

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

    public function getTelefoneProfissional(): ?string
    {
        return $this->telefone_profissional;
    }

    public function setTelefoneProfissional(string $telefone_profissional): self
    {
        $this->telefone_profissional = $telefone_profissional;

        return $this;
    }

    public function getNegativado(): ?bool
    {
        return $this->negativado;
    }

    public function setNegativado(bool $negativado): self
    {
        $this->negativado = $negativado;

        return $this;
    }

    public function getIdImportado(): ?int
    {
        return $this->id_importado;
    }

    public function setIdImportado(int $id_importado): self
    {
        $this->id_importado = $id_importado;

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCidade(): ?Cidade
    {
        return $this->cidade;
    }

    public function setCidade(?Cidade $cidade): self
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getBanco(): ?Banco
    {
        return $this->banco;
    }

    public function setBanco(?Banco $banco): self
    {
        $this->banco = $banco;

        return $this;
    }

    public function getPlanoConta(): ?PlanoConta
    {
        return $this->plano_conta;
    }

    public function setPlanoConta(?PlanoConta $plano_conta): self
    {
        $this->plano_conta = $plano_conta;

        return $this;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(string $agencia): self
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function setConta(string $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    public function setDataNascimento(?\DateTimeInterface $data_nascimento): self
    {
        $this->data_nascimento = $data_nascimento;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeInterface
    {
        return $this->data_nascimento;
    }

    /**
     *
     * @return Collection|Franqueada[]
     */
    public function getFranqueadas(): Collection
    {
        return $this->franqueadas;
    }

    public function addFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueadas->contains($franqueada) === false) {
            $this->franqueadas[] = $franqueada;
        }

        return $this;
    }

    public function removeFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueadas->contains($franqueada) === true) {
            $this->franqueadas->removeElement($franqueada);
        }

        return $this;
    }

    /**
     *
     * @return Aluno
     */
    public function getAlunos()
    {
        return $this->alunos;
    }

    public function addAluno(Aluno $aluno): self
    {
        if ($this->alunos->contains($aluno) === false) {
            $this->alunos[] = $aluno;
            $aluno->setAluno($this);
        }

        return $this;
    }

    public function removeAluno(Aluno $aluno): self
    {
        if ($this->alunos->contains($aluno) === true) {
            $this->alunos->removeElement($aluno);
            // set the owning side to null (unless already changed)
            if ($aluno->getAluno() === $this) {
                $aluno->setAluno(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Funcionario[]
     */
    public function getFuncionarios()
    {
        return $this->funcionarios;
    }

    public function addFuncionario(Funcionario $funcionario): self
    {
        if ($this->funcionarios->contains($funcionario) === false) {
            $this->funcionarios[] = $funcionario;
            $funcionario->setFuncionario($this);
        }

        return $this;
    }

    public function removeFuncionario(Funcionario $funcionario): self
    {
        if ($this->funcionarios->contains($funcionario) === true) {
            $this->funcionarios->removeElement($funcionario);
            // set the owning side to null (unless already changed)
            if ($funcionario->getFuncionario() === $this) {
                $funcionario->setFuncionario(null);
            }
        }
    }

    /**
     * @return Collection|Cheque[]
     */
    public function getChequesPessoa(): Collection
    {
        return $this->chequesPessoa;
    }

    public function addChequesPessoa(Cheque $chequesPessoa): self
    {
        if ($this->chequesPessoa->contains($chequesPessoa) === false) {
            $this->chequesPessoa[] = $chequesPessoa;
            $chequesPessoa->setPessoa($this);
        }

        return $this;
    }

    public function removeChequesPessoa(Cheque $chequesPessoa): self
    {
        if ($this->chequesPessoa->contains($chequesPessoa) === true) {
            $this->chequesPessoa->removeElement($chequesPessoa);
            // set the owning side to null (unless already changed)
            if ($chequesPessoa->getPessoa() === $this) {
                $chequesPessoa->setPessoa(null);
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
            $contrato->setResponsavelFinanceiroPessoa($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getResponsavelFinanceiroPessoa() === $this) {
                $contrato->setResponsavelFinanceiroPessoa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContaReceber[]
     */
    public function getPessoaContaReceber(): Collection
    {
        return $this->pessoaContaReceber;
    }

    public function addPessoaContaReceber(ContaReceber $pessoaContaReceber): self
    {
        if ($this->pessoaContaReceber->contains($pessoaContaReceber) === false) {
            $this->pessoaContaReceber[] = $pessoaContaReceber;
            $pessoaContaReceber->setSacadoPessoa($this);
        }

        return $this;
    }

    public function removePessoaContaReceber(ContaReceber $pessoaContaReceber): self
    {
        if ($this->pessoaContaReceber->contains($pessoaContaReceber) === true) {
            $this->pessoaContaReceber->removeElement($pessoaContaReceber);
            // set the owning side to null (unless already changed)
            if ($pessoaContaReceber->getSacadoPessoa() === $this) {
                $pessoaContaReceber->setSacadoPessoa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getTituloRecebers(): Collection
    {
        return $this->tituloRecebers;
    }

    public function addTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === false) {
            $this->tituloRecebers[] = $tituloReceber;
            $tituloReceber->setSacadoPessoa($this);
        }

        return $this;
    }

    public function removeTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === true) {
            $this->tituloRecebers->removeElement($tituloReceber);
            // set the owning side to null (unless already changed)
            if ($tituloReceber->getSacadoPessoa() === $this) {
                $tituloReceber->setSacadoPessoa(null);
            }
        }

        return $this;
    }

    public function getInadimplente(): ?bool
    {
        return $this->inadimplente;
    }

    public function setInadimplente(bool $inadimplente): self
    {
        $this->inadimplente = $inadimplente;

        return $this;
    }

    /**
     * @return Collection|Convenio[]
     */
    public function getConvenios(): Collection
    {
        return $this->convenios;
    }

    public function addConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === false) {
            $this->convenios[] = $convenio;
            $convenio->setPessoa($this);
        }

        return $this;
    }

    public function removeConvenio(Convenio $convenio): self
    {
        if ($this->convenios->contains($convenio) === true) {
            $this->convenios->removeElement($convenio);
            // set the owning side to null (unless already changed)
            if ($convenio->getPessoa() === $this) {
                $convenio->setPessoa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Interessado[]
     */
    public function getInteressados(): Collection
    {
        return $this->interessados;
    }

    public function addInteressado(Interessado $interessado): self
    {
        if ($this->interessados->contains($interessado) === false) {
            $this->interessados[] = $interessado;
            $interessado->setPessoaIndicou($this);
        }

        return $this;
    }

    public function removeInteressado(Interessado $interessado): self
    {
        if ($this->interessados->removeElement($interessado)) {
            // set the owning side to null (unless already changed)
            if ($interessado->getPessoaIndicou() === $this) {
                $interessado->setPessoaIndicou(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSponteId(): ?string
    {
        return $this->sponte_id;
    }

    /**
     * @param string|null $sponte_id
     */
    public function setSponteId(?string $sponte_id): void
    {
        $this->sponte_id = $sponte_id;
    }


}
